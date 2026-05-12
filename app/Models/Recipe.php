<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Recipe extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    /**
     * Fresh, signed URL for the stored image object key.
     * Always go through ImageUploadService so presigned URLs never get double-signed.
     */
    protected function signedImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_url
                ? app(\App\Services\ImageUploadService::class)->url($this->image_url)
                : null,
        );
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->using(RecipeIngredient::class)
            ->withPivot(['id', 'quantity', 'unit_id', 'notes', 'sort_order'])
            ->orderBy('sort_order');
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('sort_order');
    }

    public function cookingSessions(): HasMany
    {
        return $this->hasMany(CookingSession::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(RecipeCategory::class, 'category_recipe')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::deleted(function (Recipe $recipe) {
            if ($recipe->image_url) {
                app(\App\Services\ImageUploadService::class)->delete($recipe->image_url);
            }
        });
    }
}
