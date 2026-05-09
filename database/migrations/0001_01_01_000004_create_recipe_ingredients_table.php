<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('recipe_id');
            $table->uuid('ingredient_id');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->uuid('unit_id')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->foreign('recipe_id')->references('id')->on('recipes')->cascadeOnDelete();
            $table->foreign('ingredient_id')->references('id')->on('ingredients');
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();

            $table->index('recipe_id');
            $table->index('ingredient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
