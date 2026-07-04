<?php

namespace App\Console\Commands;

use App\Models\PdfGeneration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanOldPdfs extends Command
{
    protected $signature   = 'pdf:clean {--dry-run : Show what would be deleted without deleting}';
    protected $description = 'حذف ملفات PDF القديمة المنتهية الصلاحية من storage';

    public function handle(): int
    {
        $dryRun   = $this->option('dry-run');
        $deleted  = 0;
        $errors   = 0;
        $cutoff   = now()->subHours(3); // احذف بعد 3 ساعات

        $gens = PdfGeneration::where(function ($q) use ($cutoff) {
            $q->where('expires_at', '<', $cutoff)
              ->orWhere('status', 'failed')
              ->orWhere('created_at', '<', now()->subHours(6));
        })->get();

        $this->info("وُجد {$gens->count()} سجل للمعالجة...");

        foreach ($gens as $gen) {
            if ($gen->file_path && file_exists($gen->file_path)) {
                if (!$dryRun) {
                    try {
                        unlink($gen->file_path);
                        $this->line("  ✓ حُذف: " . basename($gen->file_path));
                        $deleted++;
                    } catch (\Throwable $e) {
                        $this->warn("  ✗ فشل حذف: " . $gen->file_path);
                        $errors++;
                    }
                } else {
                    $this->line("  [DRY] سيُحذف: " . basename($gen->file_path));
                    $deleted++;
                }
            }

            if (!$dryRun) {
                $gen->delete();
            }
        }

        $this->info("انتهى: حُذف $deleted ملف" . ($errors ? ", $errors أخطاء" : '') . '.');

        if ($errors > 0) {
            Log::warning('[PDF-CLEAN] Finished with errors', ['deleted' => $deleted, 'errors' => $errors]);
        }

        return self::SUCCESS;
    }
}
