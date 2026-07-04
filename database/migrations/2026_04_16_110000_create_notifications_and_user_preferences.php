<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_enrollment')->default(true);
            $table->boolean('notify_inquiry')->default(true);
            $table->boolean('notify_message')->default(true);
            $table->boolean('notify_system')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_enrollment',
                'notify_inquiry',
                'notify_message',
                'notify_system',
            ]);
        });

        Schema::dropIfExists('notifications');
    }
};
