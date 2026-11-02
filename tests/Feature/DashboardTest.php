<?php

namespace Tests\Feature;

use App\Models\Ootd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function outfit(User $user, array $attributes = []): Ootd
    {
        return $user->ootds()->create($attributes + ['image_path' => 'ootds/'.$user->id.'/'.uniqid().'.jpg']);
    }

    public function test_new_user_sees_the_onboarding_steps(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')
            ->assertOk()
            ->assertSee("Let's build your lookbook", false)
            ->assertDontSee('Suggested for you');
    }

    public function test_greeting_follows_the_time_of_day(): void
    {
        $user = User::factory()->create(['name' => 'Maria Clara']);

        $this->travelTo(now()->setTime(8, 0));
        $this->actingAs($user)->get('/dashboard')->assertSee('Good morning, Maria');

        $this->travelTo(now()->setTime(14, 0));
        $this->actingAs($user)->get('/dashboard')->assertSee('Good afternoon, Maria');

        $this->travelTo(now()->setTime(21, 0));
        $this->actingAs($user)->get('/dashboard')->assertSee('Good evening, Maria');
    }

    public function test_suggestion_is_stable_for_the_day_and_shuffle_changes_it(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 6) as $i) {
            $this->outfit($user, ['description' => "Outfit number {$i}"]);
        }

        $this->actingAs($user);

        $first = session()->get('dashboard.suggestion.id');
        $this->get('/dashboard')->assertOk()->assertSee('Suggested for you');
        $picked = session('dashboard.suggestion.id');
        $this->assertNotNull($picked);
        $this->assertNotSame($first, $picked);

        // Reloading doesn't reshuffle.
        foreach (range(1, 3) as $_) {
            $this->get('/dashboard');
            $this->assertSame($picked, session('dashboard.suggestion.id'));
        }

        // Shuffle never repeats the current one, and redirects to a clean URL.
        foreach (range(1, 5) as $_) {
            $before = session('dashboard.suggestion.id');
            $this->get('/dashboard?shuffle=1')->assertRedirect(route('dashboard'));
            $this->assertNotSame($before, session('dashboard.suggestion.id'));
        }
    }

    public function test_shuffle_with_a_single_outfit_keeps_it(): void
    {
        $user = User::factory()->create();
        $only = $this->outfit($user, ['description' => 'The only outfit']);

        $this->actingAs($user)->get('/dashboard?shuffle=1')->assertRedirect(route('dashboard'));
        $this->get('/dashboard')->assertSee('The only outfit');
        $this->assertSame($only->id, session('dashboard.suggestion.id'));
    }

    public function test_a_deleted_suggestion_is_replaced(): void
    {
        $user = User::factory()->create();
        $gone = $this->outfit($user, ['description' => 'Soon deleted']);
        $stays = $this->outfit($user, ['description' => 'Stays around']);

        $this->actingAs($user)->withSession(['dashboard.suggestion' => ['date' => today()->toDateString(), 'id' => $gone->id]]);
        $gone->delete();

        $this->get('/dashboard')->assertOk()->assertSee('Stays around')->assertDontSee('Soon deleted');
        $this->assertSame($stays->id, session('dashboard.suggestion.id'));
    }

    public function test_hero_shows_the_worn_outfit_instead_of_a_suggestion(): void
    {
        $user = User::factory()->create();
        $ootd = $this->outfit($user, ['description' => 'Casual Friday']);
        $this->outfit($user, ['description' => 'Another one']);
        $user->wearLogs()->create(['ootd_id' => $ootd->id, 'worn_on' => today()->toDateString()]);

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('Wearing today')
            ->assertSee('Casual Friday')
            ->assertSee('You&#039;re all set for today.', false)
            ->assertDontSee('Suggested for you');
    }

    public function test_month_card_counts_logged_outfits(): void
    {
        $this->travelTo(now()->startOfMonth()->addDays(9)->setTime(12, 0));
        $user = User::factory()->create();
        $a = $this->outfit($user);
        $b = $this->outfit($user);
        $user->wearLogs()->create(['ootd_id' => $a->id, 'worn_on' => today()->toDateString()]);
        $user->wearLogs()->create(['ootd_id' => $b->id, 'worn_on' => today()->subDay()->toDateString()]);
        // Last month doesn't count towards this month.
        $user->wearLogs()->create(['ootd_id' => $a->id, 'worn_on' => today()->startOfMonth()->subDay()->toDateString()]);

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('2 of the first 10 days of '.today()->format('F'), false)
            ->assertSee('outfits logged this month');
    }

    public function test_wardrobe_mix_counts_by_occasion_and_keeps_colors_stable(): void
    {
        $user = User::factory()->create();
        foreach (['work', 'work', 'work', 'casual'] as $occasion) {
            $this->outfit($user, ['occasion' => $occasion]);
        }
        $this->outfit($user);

        $html = $this->actingAs($user)->get('/dashboard')->assertOk()->getContent();

        $this->assertStringContainsString('Work: 3 outfits (60%)', $html);
        $this->assertStringContainsString('Casual: 1 outfit (20%)', $html);
        $this->assertStringContainsString('No occasion: 1 outfit (20%)', $html);
        // work is the 1st configured occasion, casual the 2nd, untagged is neutral.
        $this->assertStringContainsString('var(--series-1)', $html);
        $this->assertStringContainsString('var(--series-2)', $html);
        $this->assertStringContainsString('var(--series-other)', $html);
        $this->assertStringNotContainsString('var(--series-3)', $html);
    }

    public function test_untagged_wardrobe_nudges_instead_of_drawing_a_chart(): void
    {
        $user = User::factory()->create();
        $this->outfit($user);

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('Tag your outfits by occasion')
            ->assertDontSee('role="tooltip"', false);
    }

    public function test_not_worn_in_a_while_skips_the_featured_outfit(): void
    {
        $user = User::factory()->create();
        $worn = $this->outfit($user, ['description' => 'Worn today one']);
        foreach (['Dusty one', 'Dusty two', 'Dusty three'] as $name) {
            $this->outfit($user, ['description' => $name]);
        }
        $user->wearLogs()->create(['ootd_id' => $worn->id, 'worn_on' => today()->toDateString()]);

        $html = $this->actingAs($user)->get('/dashboard')->getContent();

        // Only look at the "Not worn in a while" section (the "Recently added" row lists everything).
        $section = str($html)->between('Not worn in a while', 'Recently added')->toString();

        $this->assertStringContainsString('Dusty one', $section);
        $this->assertStringNotContainsString('Worn today one', $section);
        // ...but the featured outfit is still in the hero card.
        $this->assertStringContainsString('Worn today one', $html);
    }
}
