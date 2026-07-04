<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp', 6); // 6-digit OTP
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            // Unique constraint: only one active OTP per email
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_otps');
    }
};
