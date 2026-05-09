<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorized_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('telegram_user_id')->unique();
            $table->string('telegram_username')->nullable();
            $table->string('display_name');
            $table->string('role')->default('user');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorized_users');
    }
};
