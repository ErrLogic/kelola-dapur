<?php

namespace Database\Seeders;

use App\Models\RecipeCategory;
use Illuminate\Database\Seeder;

class RecipeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Protein
            ['name' => 'Ayam', 'slug' => 'ayam', 'color' => '#65A30D'],
            ['name' => 'Daging', 'slug' => 'daging', 'color' => '#7C3AED'],
            ['name' => 'Ikan', 'slug' => 'ikan', 'color' => '#0284C7'],
            ['name' => 'Seafood', 'slug' => 'seafood', 'color' => '#D97706'],
            ['name' => 'Telur', 'slug' => 'telur', 'color' => '#65A30D'],
            ['name' => 'Tahu', 'slug' => 'tahu', 'color' => '#0F766E'],
            ['name' => 'Tempe', 'slug' => 'tempe', 'color' => '#4F46E5'],

            // Dish Type
            ['name' => 'Sayur', 'slug' => 'sayur', 'color' => '#0891B2'],
            ['name' => 'Sup', 'slug' => 'sup', 'color' => '#0891B2'],
            ['name' => 'Tumisan', 'slug' => 'tumisan', 'color' => '#0284C7'],
            ['name' => 'Gorengan', 'slug' => 'gorengan', 'color' => '#0891B2'],
            ['name' => 'Sambal', 'slug' => 'sambal', 'color' => '#65A30D'],
            ['name' => 'Berkuah', 'slug' => 'berkuah', 'color' => '#65A30D'],

            // Meal Time
            ['name' => 'Sarapan', 'slug' => 'sarapan', 'color' => '#78716C'],
            ['name' => 'Makan Siang', 'slug' => 'makan-siang', 'color' => '#65A30D'],
            ['name' => 'Makan Malam', 'slug' => 'makan-malam', 'color' => '#2563EB'],
            ['name' => 'Cemilan', 'slug' => 'cemilan', 'color' => '#059669'],
            ['name' => 'Dessert', 'slug' => 'dessert', 'color' => '#0369A1'],

            // Flavor
            ['name' => 'Pedas', 'slug' => 'pedas', 'color' => '#0891B2'],
            ['name' => 'Manis', 'slug' => 'manis', 'color' => '#0891B2'],
            ['name' => 'Gurih', 'slug' => 'gurih', 'color' => '#7C3AED'],
            ['name' => 'Rumahan', 'slug' => 'rumahan', 'color' => '#6D28D9'],
        ];

        foreach ($categories as $category) {
            RecipeCategory::create($category);
        }
    }
}
