<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX recipes_name_gin_idx ON recipes USING GIN (to_tsvector('simple', name))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
