<?php

namespace App\Livewire;

use App\Models\CookingSession;
use App\Models\Recipe;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    public bool $isCooking = false;

    public int $startedAtTs = 0;

    public function mount(string $id): void
    {
        $this->recipeId = $id;

        $activeSession = CookingSession::where('recipe_id', $this->recipeId)
            ->whereNull('finished_at')
            ->latest('started_at')
            ->first();

        $this->isCooking = (bool) $activeSession;
        $this->startedAtTs = $activeSession?->started_at?->timestamp ?? 0;
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
    public function activeSession(): ?CookingSession
    {
        return CookingSession::where('recipe_id', $this->recipeId)
            ->whereNull('finished_at')
            ->latest('started_at')
            ->first();
    }

    #[Computed]
    public function lastCookedAt(): ?Carbon
    {
        $max = CookingSession::where('recipe_id', $this->recipeId)
            ->whereNotNull('finished_at')
            ->max('finished_at');

        return $max ? Carbon::parse($max) : null;
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

        return $recipe->image_url;
    }

    private function formatDuration(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    public function startCooking(): void
    {
        if (! Auth::check()) {
            return;
        }

        if ($this->activeSession) {
            return;
        }

        $session = CookingSession::create([
            'recipe_id'  => $this->recipeId,
            'cooked_by'  => Auth::id(),
            'started_at' => now(),
        ]);

        unset($this->activeSession);

        $this->isCooking = true;
        $this->startedAtTs = $session->started_at->timestamp;

        $this->dispatch('cooking-started', startedAt: $session->started_at->timestamp);
    }

    public function finishCooking(): void
    {
        $session = $this->activeSession;

        if (! $session) {
            return;
        }

        $finishedAt = now();
        $session->update(['finished_at' => $finishedAt]);

        // Keep toast duration aligned with the client timer source of truth.
        $startedAtTs = $this->startedAtTs ?: ($session->started_at?->timestamp ?? $finishedAt->timestamp);
        $duration = max(0, $finishedAt->timestamp - (int) $startedAtTs);
        $formatted = $this->formatDuration($duration);

        $this->dispatch('cooking-stopped');
        $this->dispatch('toast', message: "Sesi selesai! Durasi: {$formatted}");

        unset($this->activeSession);
        unset($this->lastCookedAt);

        $this->isCooking = false;
        $this->startedAtTs = 0;
    }

    public function render()
    {
        return view('livewire.recipe-detail')
            ->title($this->recipe->name);
    }
}

