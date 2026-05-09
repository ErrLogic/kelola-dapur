<?php

namespace Tests\Feature;

use App\Livewire\RecipeForm;
use App\Livewire\RecipeIndex;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RecipePanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_pages_render_successfully(): void
    {
        $recipe = Recipe::create([
            'name' => 'Test Sup Ayam',
            'slug' => 'test-sup-ayam',
        ]);

        $this->get(route('recipes.index'))->assertOk();
        $this->get(route('recipes.create'))->assertOk();
        $this->get(route('recipes.show', ['id' => $recipe->id]))->assertOk();
        $this->get(route('recipes.edit', ['id' => $recipe->id]))->assertOk();
        $this->get(route('categories.index'))->assertOk();
    }

    public function test_recipe_can_be_created_with_categories_ingredients_and_image(): void
    {
        Storage::fake('s3');

        $ingredient = Ingredient::create([
            'name' => 'bawang putih',
            'normalized_name' => 'bawang putih',
        ]);

        $unit = Unit::create([
            'code' => 'siung',
            'name' => 'siung',
            'category' => 'QUANTITY',
        ]);

        $category = RecipeCategory::create([
            'name' => 'Rumahan',
            'slug' => 'rumahan',
        ]);

        Livewire::test(RecipeForm::class)
            ->set('name', 'Ayam Kecap Test')
            ->set('slug', 'ayam-kecap-test')
            ->set('description', 'Resep uji simpan')
            ->set('instructions', "1. Siapkan bahan\n2. Masak hingga matang")
            ->set('is_favorite', true)
            ->set('selectedCategories', [$category->id])
            ->set('ingredientRows', [[
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'quantity' => '2.5',
                'unit_id' => $unit->id,
                'notes' => 'geprek',
                'sort_order' => 1,
            ]])
            ->set('photo', UploadedFile::fake()->image('recipe.jpg', 1200, 900))
            ->call('save')
            ->assertRedirect();

        $recipe = Recipe::where('slug', 'ayam-kecap-test')->first();

        $this->assertNotNull($recipe);
        $this->assertTrue($recipe->is_favorite);
        $this->assertNotNull($recipe->image_url);
        Storage::disk('s3')->assertExists($recipe->image_url);

        $this->assertDatabaseHas('category_recipe', [
            'recipe_id' => $recipe->id,
            'recipe_category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'unit_id' => $unit->id,
            'notes' => 'geprek',
            'sort_order' => 1,
        ]);
    }

    public function test_category_can_be_toggled_when_editing_recipe(): void
    {
        $recipe = Recipe::create([
            'name' => 'Resep Uji Edit',
            'slug' => 'resep-uji-edit',
        ]);

        $categoryA = RecipeCategory::create([
            'name' => 'A',
            'slug' => 'a',
            'color' => '#65A30D',
        ]);

        $categoryB = RecipeCategory::create([
            'name' => 'B',
            'slug' => 'b',
            'color' => '#2563EB',
        ]);

        $recipe->categories()->sync([$categoryA->id]);

        Livewire::test(RecipeForm::class, ['id' => $recipe->id])
            ->assertSet('selectedCategories', [$categoryA->id])
            ->call('toggleCategory', $categoryB->id)
            ->assertSet('selectedCategories', [$categoryA->id, $categoryB->id])
            ->call('toggleCategory', $categoryA->id)
            ->assertSet('selectedCategories', [$categoryB->id]);
    }

    public function test_recipe_search_filters_only_after_three_characters(): void
    {
        Recipe::create(['name' => 'Ayam Kecap', 'slug' => 'ayam-kecap']);
        Recipe::create(['name' => 'Tempe Orek', 'slug' => 'tempe-orek']);

        Livewire::test(RecipeIndex::class)
            ->call('setSearch', 'ay')
            ->assertSee('Ayam Kecap')
            ->assertSee('Tempe Orek')
            ->call('setSearch', 'ayam')
            ->assertSee('Ayam Kecap')
            ->assertDontSee('Tempe Orek');
    }

    public function test_recipe_list_top_pills_filter_all_favorite_and_category(): void
    {
        $category = RecipeCategory::create([
            'name' => 'Ayam',
            'slug' => 'ayam',
            'color' => '#65A30D',
        ]);

        $favorite = Recipe::create([
            'name' => 'Ayam Kecap',
            'slug' => 'ayam-kecap-filter',
            'is_favorite' => true,
        ]);

        $normal = Recipe::create([
            'name' => 'Pisang Goreng',
            'slug' => 'pisang-goreng-filter',
            'is_favorite' => false,
        ]);

        $favorite->categories()->sync([$category->id]);

        Livewire::test(RecipeIndex::class)
            ->assertSee('Ayam Kecap')
            ->assertSee('Pisang Goreng')
            ->call('toggleFavoriteFilter')
            ->assertSee('Ayam Kecap')
            ->assertDontSee('Pisang Goreng')
            ->call('setCategoryFilter', $category->id)
            ->assertSee('Ayam Kecap')
            ->call('showAll')
            ->assertSee('Ayam Kecap')
            ->assertSee('Pisang Goreng');
    }
}

