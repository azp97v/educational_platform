<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        return (bool) DB::selectOne(
            "SELECT 1 FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = ? AND index_name = ?",
            [$table, $indexName]
        );
    }

    public function up(): void
    {
        if (!$this->indexExists('messages', 'idx_messages_content_ft')) {
            DB::statement('ALTER TABLE messages ADD FULLTEXT idx_messages_content_ft (content)');
        }

        if (!$this->indexExists('users', 'idx_users_name_email_ft')) {
            DB::statement('ALTER TABLE users ADD FULLTEXT idx_users_name_email_ft (name, email)');
        }

        if (!$this->indexExists('courses', 'idx_courses_search_ft')) {
            DB::statement('ALTER TABLE courses ADD FULLTEXT idx_courses_search_ft (name, description)');
        }
    }

    public function down(): void
    {
        if ($this->indexExists('messages', 'idx_messages_content_ft')) {
            DB::statement('ALTER TABLE messages DROP INDEX idx_messages_content_ft');
        }
        if ($this->indexExists('users', 'idx_users_name_email_ft')) {
            DB::statement('ALTER TABLE users DROP INDEX idx_users_name_email_ft');
        }
        if ($this->indexExists('courses', 'idx_courses_search_ft')) {
            DB::statement('ALTER TABLE courses DROP INDEX idx_courses_search_ft');
        }
    }
};
