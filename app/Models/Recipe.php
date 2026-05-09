<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

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
}
