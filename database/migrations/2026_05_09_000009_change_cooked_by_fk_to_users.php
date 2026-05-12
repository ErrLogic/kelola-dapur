<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cooking_sessions', function (Blueprint $table) {
            $table->dropForeign(['cooked_by']);
            $table->foreign('cooked_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('cooking_sessions', function (Blueprint $table) {
            $table->dropForeign(['cooked_by']);
            $table->foreign('cooked_by')->references('id')->on('authorized_users');
        });
    }
};
