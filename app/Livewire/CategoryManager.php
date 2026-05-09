<?php

namespace App\Livewire;

use App\Models\RecipeCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kategori')]
class CategoryManager extends Component
{
    public string $newCategoryName = '';
    public string $newCategoryColor = '#65A30D';
    public ?string $editingId = null;
    public string $editingName = '';
    public string $editingColor = '#65A30D';

    public function addCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:100|unique:recipe_categories,name',
            'newCategoryColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ], [
            'newCategoryName.required' => 'Nama kategori harus diisi.',
            'newCategoryName.unique' => 'Kategori sudah ada.',
            'newCategoryColor.regex' => 'Format warna tidak valid.',
        ]);

        RecipeCategory::create([
            'name' => trim($this->newCategoryName),
            'slug' => Str::slug($this->newCategoryName),
            'color' => strtoupper($this->newCategoryColor),
        ]);

        $this->newCategoryName = '';
        $this->newCategoryColor = '#65A30D';
        unset($this->categories);
        $this->dispatch('toast', message: 'Kategori ditambahkan');
    }

    public function startEditing(string $id): void
    {
        $category = RecipeCategory::findOrFail($id);

        $this->editingId = $id;
        $this->editingName = $category->name;
        $this->editingColor = $this->normalizeHex($category->color);
    }

    public function saveEditing(): void
    {
        $this->validate([
            'editingName' => 'required|string|max:100|unique:recipe_categories,name,' . $this->editingId,
            'editingColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        $category = RecipeCategory::findOrFail($this->editingId);
        $category->update([
            'name' => trim($this->editingName),
            'slug' => Str::slug($this->editingName),
            'color' => strtoupper($this->editingColor),
        ]);

        $this->editingId = null;
        $this->editingName = '';
        $this->editingColor = '#65A30D';
        unset($this->categories);
        $this->dispatch('toast', message: 'Kategori diperbarui');
    }

    public function cancelEditing(): void
    {
        $this->editingId = null;
        $this->editingName = '';
        $this->editingColor = '#65A30D';
    }

    public function deleteCategory(string $id): void
    {
        RecipeCategory::findOrFail($id)->delete();
        unset($this->categories);
        $this->dispatch('toast', message: 'Kategori dihapus');
    }

    #[Computed]
    public function categories()
    {
        return RecipeCategory::withCount('recipes')->orderBy('name')->get();
    }

    #[Computed]
    public function colorPresets(): Collection
    {
        return collect([
            '#65A30D', '#2563EB', '#0891B2', '#7C3AED', '#DB2777', '#D97706', '#0F766E', '#6D28D9',
            '#0284C7', '#4F46E5', '#0D9488', '#64748B',
        ]);
    }

    public function categoryPillStyle(?string $color): string
    {
        $hex = $this->normalizeHex($color);
        [$r, $g, $b] = $this->hexToRgb($hex);

        return sprintf('background:rgba(%d,%d,%d,.20);border-color:rgba(%d,%d,%d,.35);color:%s;', $r, $g, $b, $r, $g, $b, $hex);
    }

    protected function normalizeHex(?string $color): string
    {
        if (! is_string($color)) {
            return '#64748B';
        }

        $trimmed = trim($color);

        return preg_match('/^#([A-Fa-f0-9]{6})$/', $trimmed) ? strtoupper($trimmed) : '#64748B';
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

    public function render()
    {
        return view('livewire.category-manager');
    }
}
