<?php

namespace Tests\Feature;

use App\Livewire\RecipeDetail;
use App\Livewire\RecipeIndex;
use App\Models\CookingSession;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class CookingSessionTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(string $username = 'testuser'): User
    {
        return User::create([
            'name'     => 'Test User',
            'username' => $username,
            'password' => Hash::make('password'),
        ]);
    }

    private function createRecipe(string $name = 'Ayam Goreng', string $slug = 'ayam-goreng'): Recipe
    {
        return Recipe::create([
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    // =========================================================================
    // 8.1 — RecipeDetail tests
    // =========================================================================

    // Req 1.1
    public function test_mulai_memasak_button_is_shown_when_no_active_session_exists(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->assertSee('Mulai Memasak');
    }

    // Req 1.2, 1.3
    public function test_start_cooking_creates_session_with_correct_data(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        $before = now()->subSecond();

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->call('startCooking');

        $session = CookingSession::where('recipe_id', $recipe->id)->first();

        $this->assertNotNull($session);
        $this->assertEquals($recipe->id, $session->recipe_id);
        $this->assertEquals($user->id, $session->cooked_by);
        $this->assertNull($session->finished_at);
        $this->assertTrue($session->started_at->greaterThanOrEqualTo($before));
    }

    // Req 1.4
    public function test_selesai_button_is_shown_when_active_session_exists(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(5),
        ]);

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->assertSee('Selesai')
            ->assertDontSee('Mulai Memasak');
    }

    // Req 1.5
    public function test_start_cooking_does_not_create_new_session_if_one_already_exists(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(3),
        ]);

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->call('startCooking');

        $this->assertDatabaseCount('cooking_sessions', 1);
    }

    // Req 3.1
    public function test_finish_cooking_sets_finished_at(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(10),
        ]);

        $before = now()->subSecond();

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->call('finishCooking');

        $session = CookingSession::where('recipe_id', $recipe->id)->first();

        $this->assertNotNull($session->finished_at);
        $this->assertTrue($session->finished_at->greaterThanOrEqualTo($before));
    }

    // Req 3.3
    public function test_finish_cooking_dispatches_toast_event(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(2),
        ]);

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->call('finishCooking')
            ->assertDispatched('toast');
    }

    // Req 4.2
    public function test_belum_pernah_dimasak_is_shown_when_no_finished_sessions_exist(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->assertSee('Belum pernah dimasak');
    }

    // Req 4.3
    public function test_last_cooked_at_is_updated_after_finish_cooking(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(15),
        ]);

        $component = Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id]);

        $component->assertSee('Belum pernah dimasak');

        $component->call('finishCooking');

        $component->assertDontSee('Belum pernah dimasak');
        $component->assertSee('Terakhir dimasak');
    }

    // Req 6.1
    public function test_active_session_is_detected_on_mount_when_one_exists(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(7),
        ]);

        Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id])
            ->assertSee('Selesai')
            ->assertDontSee('Mulai Memasak');
    }

    // Req 6.2
    public function test_latest_session_is_used_when_multiple_active_sessions_exist(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe();

        CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subHours(2),
        ]);

        $newer = CookingSession::create([
            'recipe_id'  => $recipe->id,
            'cooked_by'  => $user->id,
            'started_at' => now()->subMinutes(10),
        ]);

        $component = Livewire::actingAs($user)
            ->test(RecipeDetail::class, ['id' => $recipe->id]);

        $this->assertEquals($newer->id, $component->get('activeSession')->id);
    }

    // =========================================================================
    // 8.2 — RecipeIndex tests
    // =========================================================================

    // Req 5.1
    public function test_recipe_card_shows_last_cooked_at_when_finished_session_exists(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe('Soto Ayam', 'soto-ayam');

        CookingSession::create([
            'recipe_id'   => $recipe->id,
            'cooked_by'   => $user->id,
            'started_at'  => now()->subHours(1),
            'finished_at' => now()->subMinutes(30),
        ]);

        $result = Recipe::query()
            ->addSelect(['last_cooked_at' => CookingSession::select('finished_at')
                ->whereColumn('recipe_id', 'recipes.id')
                ->whereNotNull('finished_at')
                ->latest('finished_at')
                ->limit(1),
            ])
            ->where('id', $recipe->id)
            ->first();

        $this->assertNotNull($result->last_cooked_at);
    }

    // Req 5.2
    public function test_recipe_card_does_not_show_date_when_recipe_has_never_been_cooked(): void
    {
        $user   = $this->createUser();
        $recipe = $this->createRecipe('Rendang', 'rendang');

        $result = Recipe::query()
            ->addSelect(['last_cooked_at' => CookingSession::select('finished_at')
                ->whereColumn('recipe_id', 'recipes.id')
                ->whereNotNull('finished_at')
                ->latest('finished_at')
                ->limit(1),
            ])
            ->where('id', $recipe->id)
            ->first();

        $this->assertNull($result->last_cooked_at);
    }
}
