<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_recipe', function (Blueprint $table) {
            $table->uuid('recipe_id');
            $table->uuid('recipe_category_id');
            $table->primary(['recipe_id', 'recipe_category_id']);

            $table->foreign('recipe_id')->references('id')->on('recipes')->cascadeOnDelete();
            $table->foreign('recipe_category_id')->references('id')->on('recipe_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_recipe');
    }
};
