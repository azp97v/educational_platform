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
        Schema::create('system_errors', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->index();
            $table->text('message');
            $table->string('file', 500)->nullable();
            $table->unsignedSmallInteger('line')->nullable();
            $table->string('url', 1000)->nullable();
            $table->string('method', 10)->nullable();
            $table->unsignedSmallInteger('status_code')->default(500)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_ip', 45)->nullable();
            $table->longText('trace')->nullable();
            $table->boolean('resolved')->default(false)->index();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'resolved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_errors');
    }
};
