<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipe_categories', function (Blueprint $table) {
            $table->string('color', 7)->default('#64748B')->after('slug');
        });

        $fallbackPalette = ['#65A30D', '#2563EB', '#0891B2', '#7C3AED', '#DB2777', '#D97706', '#0F766E', '#4F46E5'];

        $categories = DB::table('recipe_categories')->orderBy('name')->get(['id', 'color']);

        foreach ($categories as $index => $category) {
            if (! is_string($category->color) || ! preg_match('/^#([A-Fa-f0-9]{6})$/', $category->color)) {
                DB::table('recipe_categories')
                    ->where('id', $category->id)
                    ->update(['color' => $fallbackPalette[$index % count($fallbackPalette)]]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('recipe_categories', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};

