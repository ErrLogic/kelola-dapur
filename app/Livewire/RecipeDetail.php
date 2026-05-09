<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Services\ImageUploadService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.app')]
#[Title('Detail Resep')]
class RecipeDetail extends Component
{
    #[Locked]
    public string $recipeId;

    public function mount(string $id): void
    {
        $this->recipeId = $id;
    }

    public function toggleFavorite(): void
    {
        $recipe = Recipe::findOrFail($this->recipeId);
        $recipe->update(['is_favorite' => !$recipe->is_favorite]);
        $this->dispatch('toast', message: $recipe->is_favorite ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit');
    }

    public function deleteRecipe(): void
    {
        $recipe = Recipe::findOrFail($this->recipeId);
        $recipe->delete();
        $this->dispatch('toast', message: 'Resep berhasil dihapus');
        $this->redirect(route('recipes.index'), navigate: true);
    }

    #[Computed]
    public function recipe()
    {
        return Recipe::with(['categories', 'ingredients' => function ($q) {
            $q->orderBy('sort_order');
        }])->findOrFail($this->recipeId);
    }

    public function getImageUrl(): ?string
    {
        $recipe = $this->recipe;
        if (!$recipe->image_url) return null;

        if (str_starts_with($recipe->image_url, 'http')) {
            return $recipe->image_url;
        }

        return app(ImageUploadService::class)->url($recipe->image_url);
    }

    public function render()
    {
        return view('livewire.recipe-detail')
            ->title($this->recipe->name);
    }
}

