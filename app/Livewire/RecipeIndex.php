<?php

namespace App\Livewire;

use App\Models\CookingSession;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Kelola Dapur')]
class RecipeIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $filter = '';

    public function setSearch(string $value): void
    {
        $this->search = trim($value);
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function setCategoryFilter(string $id): void
    {
        $this->category = $this->category === $id ? '' : $id;
        $this->resetPage();
    }

    public function showAll(): void
    {
        $this->filter = '';
        $this->category = '';
        $this->resetPage();
    }

    public function toggleFavoriteFilter(): void
    {
        $this->filter = $this->filter === 'favorite' ? '' : 'favorite';
        $this->resetPage();
    }

    public function toggleFavorite(string $id): void
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->update(['is_favorite' => ! $recipe->is_favorite]);

        $this->dispatch('toast', message: $recipe->is_favorite ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit');
    }

    public function deleteRecipe(string $id): void
    {
        $recipe = Recipe::findOrFail($id);
        $recipeName = $recipe->name;
        $recipe->delete();

        $this->dispatch('toast', message: "\"$recipeName\" berhasil dihapus");
    }

    #[Computed]
    public function recipes()
    {
        $search = trim($this->search);

        return Recipe::query()
            ->with('categories')
            ->addSelect(['last_cooked_at' => CookingSession::select('finished_at')
                ->whereColumn('recipe_id', 'recipes.id')
                ->whereNotNull('finished_at')
                ->latest('finished_at')
                ->limit(1),
            ])
            ->when(mb_strlen($search) >= 3, function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($search) . '%']);
            })
            ->when($this->category, function ($query) {
                $query->whereHas('categories', fn ($subQuery) => $subQuery->where('recipe_categories.id', $this->category));
            })
            ->when($this->filter === 'favorite', function ($query) {
                $query->where('is_favorite', true);
            })
            ->orderByDesc('is_favorite')
            ->orderBy('name')
            ->paginate(5);
    }

    #[Computed]
    public function categories()
    {
        return RecipeCategory::orderBy('name')->get();
    }

    public function categoryPillStyle(?string $color, bool $selected = false): string
    {
        $hex = $this->normalizeHex($color);
        [$r, $g, $b] = $this->hexToRgb($hex);

        if ($selected) {
            return sprintf('background:%s;border-color:%s;color:%s;', $hex, $hex, $this->contrastTextColor($hex));
        }

        return sprintf('background:rgba(%d,%d,%d,.20);border-color:rgba(%d,%d,%d,.35);color:%s;', $r, $g, $b, $r, $g, $b, $hex);
    }

    public function categoryBadgeStyle(?string $color, bool $selected = false): string
    {
        $hex = $this->normalizeHex($color);
        [$r, $g, $b] = $this->hexToRgb($hex);

        if ($selected) {
            return 'background:rgba(255,255,255,.26);color:' . $this->contrastTextColor($hex) . ';';
        }

        return sprintf('background:rgba(%d,%d,%d,.30);color:%s;', $r, $g, $b, $hex);
    }

    protected function normalizeHex(?string $color): string
    {
        if (! is_string($color)) {
            return '#64748b';
        }

        $trimmed = trim($color);

        return preg_match('/^#([A-Fa-f0-9]{6})$/', $trimmed) ? strtoupper($trimmed) : '#64748b';
    }

    protected function hexToRgb(string $hex): array
    {
        $value = ltrim($hex, '#');

        return [
            hexdec(substr($value, 0, 2)),
            hexdec(substr($value, 2, 2)),
            hexdec(substr($value, 4, 2)),
        ];
    }

    protected function contrastTextColor(string $hex): string
    {
        [$r, $g, $b] = $this->hexToRgb($hex);
        $luminance = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $luminance > 155 ? '#111827' : '#FFFFFF';
    }

    public function render()
    {
        return view('livewire.recipe-index');
    }
}

