<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // FULLTEXT on messages.content for searchMessages()
        DB::statement('ALTER TABLE messages ADD FULLTEXT idx_messages_content_ft (content)');

        // FULLTEXT on users.name + email for searchUsers()
        DB::statement('ALTER TABLE users ADD FULLTEXT idx_users_name_email_ft (name, email)');

        // FULLTEXT on courses.title + description for course search
        DB::statement('ALTER TABLE courses ADD FULLTEXT idx_courses_search_ft (title, description)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE messages DROP INDEX idx_messages_content_ft');
        DB::statement('ALTER TABLE users DROP INDEX idx_users_name_email_ft');
        DB::statement('ALTER TABLE courses DROP INDEX idx_courses_search_ft');
    }
};
