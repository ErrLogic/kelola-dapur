<?php

namespace App\Livewire;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeIngredient;
use App\Models\Unit;
use App\Services\ImageUploadService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class RecipeForm extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?string $recipeId = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $instructions = '';
    public bool $is_favorite = false;
    public array $selectedCategories = [];
    public mixed $photo = null;
    public ?string $existingImageUrl = null;
    public bool $removeImage = false;

    public array $ingredientRows = [];
    public string $ingredientSearch = '';
    public int $activeIngredientRow = -1;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:recipes,slug,' . $this->recipeId],
            'description' => ['nullable', 'string', 'max:1000'],
            'instructions' => ['nullable', 'string'],
            'is_favorite' => ['boolean'],
            'selectedCategories' => ['array'],
            'selectedCategories.*' => ['exists:recipe_categories,id'],
            'photo' => ['nullable', 'file', 'extensions:jpeg,png,jpg,gif,svg,webp,avif,heic,heif,bmp,tiff', 'max:5120'],
            'ingredientRows' => ['array', 'min:1'],
            'ingredientRows.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'ingredientRows.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'ingredientRows.*.unit_id' => ['nullable', 'exists:units,id'],
            'ingredientRows.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama resep harus diisi.',
            'slug.required' => 'Slug harus diisi.',
            'slug.unique' => 'Slug sudah digunakan.',
            'photo.extensions' => 'File harus berupa gambar (jpeg, png, gif, svg, webp, avif, heic, bmp, tiff).',
            'photo.file' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran gambar maksimal 5MB.',
            'ingredientRows.min' => 'Tambahkan minimal satu bahan.',
            'ingredientRows.*.ingredient_id.required' => 'Pilih bahan terlebih dahulu.',
        ];
    }

    public function mount(?string $id = null): void
    {
        if (! $id) {
            $this->addIngredientRow();

            return;
        }

        $recipe = Recipe::with(['ingredients', 'categories'])->findOrFail($id);

        $this->recipeId = $recipe->id;
        $this->name = $recipe->name;
        $this->slug = $recipe->slug;
        $this->description = $recipe->description ?? '';
        $this->instructions = $recipe->instructions ?? '';
        $this->is_favorite = $recipe->is_favorite;
        $this->existingImageUrl = $recipe->image_url;
        $this->selectedCategories = $recipe->categories->pluck('id')->all();
        $this->ingredientRows = $recipe->ingredients
            ->map(fn (Ingredient $ingredient) => [
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'quantity' => $ingredient->pivot->quantity !== null ? (string) $ingredient->pivot->quantity : '',
                'unit_id' => $ingredient->pivot->unit_id ?? '',
                'notes' => $ingredient->pivot->notes ?? '',
                'sort_order' => (int) $ingredient->pivot->sort_order,
            ])
            ->sortBy('sort_order')
            ->values()
            ->all();

        if (empty($this->ingredientRows)) {
            $this->addIngredientRow();
        }
    }

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function updatedSlug(): void
    {
        // slug is always auto-generated from name; this method is kept for compatibility
    }

    public function updatedPhoto(): void
    {
        $this->validateOnly('photo');
        $this->removeImage = false;
    }

    public function removePhoto(): void
    {
        $this->photo = null;
        $this->removeImage = true;
    }

    public function toggleCategory(string $categoryId): void
    {
        $categoryId = (string) $categoryId;
        $selected = collect($this->selectedCategories)
            ->map(fn ($id) => (string) $id)
            ->values();

        if ($selected->contains($categoryId)) {
            $this->selectedCategories = $selected
                ->reject(fn ($id) => $id === $categoryId)
                ->values()
                ->all();

            return;
        }

        $this->selectedCategories = $selected
            ->push($categoryId)
            ->unique()
            ->values()
            ->all();
    }

    public function isCategorySelected(string $categoryId): bool
    {
        $categoryId = (string) $categoryId;

        return collect($this->selectedCategories)
            ->map(fn ($id) => (string) $id)
            ->contains($categoryId);
    }

    public function addIngredientRow(): void
    {
        $this->ingredientRows[] = [
            'ingredient_id' => '',
            'ingredient_name' => '',
            'quantity' => '',
            'unit_id' => '',
            'notes' => '',
            'sort_order' => count($this->ingredientRows) + 1,
        ];
    }

    public function removeIngredientRow(int $index): void
    {
        unset($this->ingredientRows[$index]);
        $this->ingredientRows = array_values($this->ingredientRows);
        $this->reindexSortOrder();

        if (empty($this->ingredientRows)) {
            $this->addIngredientRow();
        }
    }

    public function moveIngredientUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $rows = $this->ingredientRows;
        [$rows[$index - 1], $rows[$index]] = [$rows[$index], $rows[$index - 1]];
        $this->ingredientRows = $rows;
        $this->reindexSortOrder();
    }

    public function moveIngredientDown(int $index): void
    {
        if ($index >= count($this->ingredientRows) - 1) {
            return;
        }

        $rows = $this->ingredientRows;
        [$rows[$index + 1], $rows[$index]] = [$rows[$index], $rows[$index + 1]];
        $this->ingredientRows = $rows;
        $this->reindexSortOrder();
    }

    public function openIngredientSearch(int $rowIndex): void
    {
        $this->activeIngredientRow = $rowIndex;
        $this->ingredientSearch = $this->ingredientRows[$rowIndex]['ingredient_name'] ?? '';
    }

    public function closeIngredientSearch(): void
    {
        $this->activeIngredientRow = -1;
        $this->ingredientSearch = '';
    }

    public function selectIngredient(int $rowIndex, string $ingredientId, string $ingredientName): void
    {
        $this->ingredientRows[$rowIndex]['ingredient_id'] = $ingredientId;
        $this->ingredientRows[$rowIndex]['ingredient_name'] = $ingredientName;
        $this->closeIngredientSearch();
    }

    public function createAndSelectIngredient(int $rowIndex): void
    {
        if (blank(trim($this->ingredientSearch))) {
            return;
        }

        $name = trim($this->ingredientSearch);

        $ingredient = Ingredient::firstOrCreate(
            ['name' => $name],
            ['normalized_name' => Str::lower($name)]
        );

        $this->selectIngredient($rowIndex, $ingredient->id, $ingredient->name);
        $this->dispatch('toast', message: 'Bahan baru ditambahkan');
    }

    #[Computed]
    public function filteredIngredients(): Collection
    {
        if ($this->activeIngredientRow < 0) {
            return collect();
        }

        return Ingredient::query()
            ->when(
                filled($this->ingredientSearch),
                fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', ['%' . Str::lower($this->ingredientSearch) . '%'])
            )
            ->orderBy('name')
            ->limit(filled($this->ingredientSearch) ? 20 : 12)
            ->get();
    }

    #[Computed]
    public function allUnits(): Collection
    {
        return Unit::orderByRaw("CASE category WHEN 'SPOON' THEN 1 WHEN 'VOLUME' THEN 2 WHEN 'WEIGHT' THEN 3 WHEN 'QUANTITY' THEN 4 WHEN 'SPECIAL' THEN 5 ELSE 6 END")
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function units(): Collection
    {
        return $this->allUnits->groupBy('category');
    }

    #[Computed]
    public function categories(): Collection
    {
        return RecipeCategory::withCount('recipes')->orderBy('name')->get();
    }

    public function categoryPillStyle(?string $color, bool $selected = false): string
    {
        $hex = $this->normalizeHex($color);
        [$r, $g, $b] = $this->hexToRgb($hex);

        if ($selected) {
            return sprintf('background:%s;border-color:%s;color:%s;', $hex, $hex, $this->contrastTextColor($hex));
        }

        return sprintf('background:rgba(%d,%d,%d,.18);border-color:rgba(%d,%d,%d,.34);color:%s;', $r, $g, $b, $r, $g, $b, $hex);
    }

    public function categoryBadgeStyle(?string $color, bool $selected = false): string
    {
        $hex = $this->normalizeHex($color);
        [$r, $g, $b] = $this->hexToRgb($hex);

        if ($selected) {
            return 'background:rgba(255,255,255,.28);color:' . $this->contrastTextColor($hex) . ';';
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

    public function imagePreviewUrl(): ?string
    {
        if ($this->photo) {
            return $this->photo->temporaryUrl();
        }

        if (! $this->existingImageUrl || $this->removeImage) {
            return null;
        }

        return app(ImageUploadService::class)->url($this->existingImageUrl);
    }

    public function save(): void
    {
        $this->ingredientRows = $this->normalizedIngredientRows();

        if (empty($this->ingredientRows)) {
            $this->addError('ingredientRows', 'Tambahkan minimal satu bahan.');

            return;
        }

        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            $imageUrl = $this->resolveImagePath();

            $recipe = $this->recipeId
                ? tap(Recipe::findOrFail($this->recipeId))->update([
                    'name' => $validated['name'],
                    'slug' => $validated['slug'],
                    'description' => $validated['description'] ?: null,
                    'instructions' => $validated['instructions'] ?: null,
                    'image_url' => $imageUrl,
                    'is_favorite' => $validated['is_favorite'],
                ])
                : Recipe::create([
                    'name' => $validated['name'],
                    'slug' => $validated['slug'],
                    'description' => $validated['description'] ?: null,
                    'instructions' => $validated['instructions'] ?: null,
                    'image_url' => $imageUrl,
                    'is_favorite' => $validated['is_favorite'],
                ]);

            $this->recipeId = $recipe->id;
            $recipe->categories()->sync($validated['selectedCategories'] ?? []);
            $recipe->recipeIngredients()->delete();

            foreach ($this->ingredientRows as $row) {
                RecipeIngredient::query()->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $row['ingredient_id'],
                    'quantity' => $row['quantity'] !== '' ? $row['quantity'] : null,
                    'unit_id' => $row['unit_id'] !== '' ? $row['unit_id'] : null,
                    'notes' => filled($row['notes']) ? trim($row['notes']) : null,
                    'sort_order' => $row['sort_order'],
                ]);
            }

            $this->existingImageUrl = $imageUrl;
            $this->photo = null;
            $this->removeImage = false;
        });

        $this->dispatch('toast', message: 'Resep berhasil disimpan');
        $this->redirectRoute('recipes.show', ['id' => $this->recipeId], navigate: true);
    }

    protected function resolveImagePath(): ?string
    {
        $uploadService = app(ImageUploadService::class);

        if ($this->photo) {
            return $uploadService->upload($this->photo, $this->slug, $this->existingImageUrl);
        }

        if ($this->removeImage && $this->existingImageUrl) {
            $uploadService->delete($this->existingImageUrl);

            return null;
        }

        return $this->existingImageUrl;
    }

    protected function normalizedIngredientRows(): array
    {
        return collect($this->ingredientRows)
            ->map(fn (array $row) => [
                'ingredient_id' => $row['ingredient_id'] ?? '',
                'ingredient_name' => $row['ingredient_name'] ?? '',
                'quantity' => isset($row['quantity']) ? trim((string) $row['quantity']) : '',
                'unit_id' => $row['unit_id'] ?? '',
                'notes' => isset($row['notes']) ? trim((string) $row['notes']) : '',
                'sort_order' => (int) ($row['sort_order'] ?? 0),
            ])
            ->filter(function (array $row) {
                return filled($row['ingredient_id'])
                    || filled($row['ingredient_name'])
                    || filled($row['quantity'])
                    || filled($row['unit_id'])
                    || filled($row['notes']);
            })
            ->values()
            ->map(function (array $row, int $index) {
                $row['sort_order'] = $index + 1;

                return $row;
            })
            ->all();
    }

    protected function reindexSortOrder(): void
    {
        foreach ($this->ingredientRows as $index => &$row) {
            $row['sort_order'] = $index + 1;
        }

        unset($row);
    }

    public function render()
    {
        return view('livewire.recipe-form')
            ->title($this->recipeId ? 'Edit · ' . $this->name : 'Resep Baru');
    }
}

