<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToR2 extends Command
{
    protected $signature = 'media:migrate-to-r2
                            {--dry-run : Show what would be migrated without actually doing it}
                            {--table=all : Which table to migrate (all, users, lessons, messages, statuses, stickers)}';

    protected $description = 'Migrate media files from local public disk to Cloudflare R2';

    private int $migrated = 0;
    private int $skipped = 0;
    private int $failed = 0;

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $table = $this->option('table');

        if (config('media.disk') !== 'r2') {
            $this->error('MEDIA_DISK must be set to "r2" before running migration.');
            return 1;
        }

        if ($isDryRun) {
            $this->warn('DRY RUN — no files will be moved');
        }

        $tables = $table === 'all'
            ? ['users', 'lessons', 'messages', 'user_statuses', 'stickers', 'groups']
            : [$table];

        foreach ($tables as $t) {
            $this->migrateTable($t, $isDryRun);
        }

        $this->newLine();
        $this->table(['Migrated', 'Skipped', 'Failed'], [[$this->migrated, $this->skipped, $this->failed]]);

        return $this->failed > 0 ? 1 : 0;
    }

    private function migrateTable(string $table, bool $isDryRun): void
    {
        $columns = match ($table) {
            'users' => ['id', 'avatar_url'],
            'lessons' => ['id', 'video_file', 'audio_file'],
            'messages' => ['id', 'attachment_path'],
            'user_statuses' => ['id', 'content_url', 'audio_url'],
            'stickers' => ['id', 'file_path'],
            'groups' => ['id', 'avatar_path'],
            default => null,
        };

        if (!$columns) {
            $this->warn("Unknown table: {$table}");
            return;
        }

        $this->info("Migrating {$table}...");

        $idColumn = array_shift($columns);
        $rows = DB::table($table)->whereNotNull($columns[0])->get(array_merge([$idColumn], $columns));

        foreach ($rows as $row) {
            foreach ($columns as $col) {
                $path = $row->$col ?? null;
                if (!$path) continue;

                $this->migrateFile($table, $idColumn, $row->$idColumn, $col, $path, $isDryRun);
            }
        }
    }

    private function migrateFile(string $table, string $idCol, int $id, string $column, string $path, bool $isDryRun): void
    {
        // Skip URLs already pointing to R2 / CDN
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $this->skipped++;
            return;
        }

        $localDisk = Storage::disk('public');
        $r2Disk = Storage::disk('r2');

        if (!$localDisk->exists($path)) {
            $this->warn("  Missing: {$path}");
            $this->skipped++;
            return;
        }

        if ($isDryRun) {
            $this->line("  Would migrate: {$path}");
            $this->migrated++;
            return;
        }

        try {
            $stream = $localDisk->readStream($path);
            $r2Disk->writeStream($path, $stream, ['visibility' => 'public']);

            DB::table($table)->where($idCol, $id)->update([$column => $path]);

            $this->migrated++;
        } catch (\Throwable $e) {
            $this->error("  Failed {$path}: {$e->getMessage()}");
            $this->failed++;
        }
    }
}
