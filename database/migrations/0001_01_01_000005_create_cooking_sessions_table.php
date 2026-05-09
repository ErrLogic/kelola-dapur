<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooking_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('recipe_id');
            $table->uuid('cooked_by');
            $table->timestampTz('started_at')->useCurrent();
            $table->timestampTz('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->timestampsTz();

            $table->foreign('recipe_id')->references('id')->on('recipes');
            $table->foreign('cooked_by')->references('id')->on('authorized_users');

            $table->index('recipe_id');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX cooking_sessions_started_at_desc_idx ON cooking_sessions (started_at DESC)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cooking_sessions');
    }
};
