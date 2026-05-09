<?php

use App\Livewire\CategoryManager;
use App\Livewire\Login;
use App\Livewire\RecipeDetail;
use App\Livewire\RecipeForm;
use App\Livewire\RecipeIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/', RecipeIndex::class)->name('recipes.index');
    Route::get('/recipes/create', RecipeForm::class)->name('recipes.create');
    Route::get('/recipes/{id}', RecipeDetail::class)->name('recipes.show');
    Route::get('/recipes/{id}/edit', RecipeForm::class)->name('recipes.edit');
    Route::get('/categories', CategoryManager::class)->name('categories.index');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');
