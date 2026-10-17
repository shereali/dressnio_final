<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('login_codes')) {
            return;
        }

        // The deploy script and the container entrypoint can both run migrations at once.
        // If the other run created the table first, there is nothing left to do.
        try {
            $this->createTable();
        } catch (QueryException $e) {
            if (! Schema::hasTable('login_codes')) {
                throw $e;
            }
        }
    }

    private function createTable(): void
    {
        Schema::create('login_codes', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_no')->unique();
            $table->string('code'); // sha256 of the OTP, never the plain code
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_codes');
    }
};
