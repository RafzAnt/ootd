<?php

namespace Tests\Feature;

use App\Models\Ootd;
use App\Models\User;
use App\Services\OutfitPicker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WardrobeTest extends TestCase
{
    use RefreshDatabase;

    private function outfit(User $user, array $attributes = []): Ootd
    {
        return $user->ootds()->create($attributes + ['image_path' => 'ootds/'.$user->id.'/'.uniqid().'.jpg']);
    }

    public function test_outfit_can_be_tagged_with_an_occasion(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $photo = UploadedFile::fake()->createWithContent('fit.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        $this->actingAs($user)->post('/ootds', ['photo' => $photo, 'occasion' => 'work'])->assertSessionHasNoErrors();
        $this->assertSame('work', $user->ootds()->first()->occasion);

        $this->actingAs($user)
            ->post('/ootds', ['photo' => $photo, 'occasion' => 'not-a-real-one'])
            ->assertSessionHasErrors('occasion');
    }

    public function test_favorite_toggles_and_returns_json_for_ajax(): void
    {
        $user = User::factory()->create();
        $ootd = $this->outfit($user);

        $this->actingAs($user)->postJson("/ootds/{$ootd->id}/favorite")->assertOk()->assertJson(['favorite' => true]);
        $this->assertTrue($ootd->fresh()->is_favorite);

        $this->actingAs($user)->postJson("/ootds/{$ootd->id}/favorite")->assertJson(['favorite' => false]);
        $this->assertFalse($ootd->fresh()->is_favorite);
    }

    public function test_wearing_an_outfit_logs_today_and_replaces_an_earlier_choice(): void
    {
        $user = User::factory()->create();
        $first = $this->outfit($user);
        $second = $this->outfit($user);

        $this->actingAs($user)->post("/ootds/{$first->id}/wear");
        $this->assertSame($first->id, $user->wornToday()->id);

        $this->actingAs($user)->post("/ootds/{$second->id}/wear", ['then' => 'dashboard'])
            ->assertRedirect(route('dashboard'));

        $this->assertSame(1, $user->wearLogs()->count());
        $this->assertSame($second->id, $user->fresh()->wornToday()->id);

        $this->actingAs($user)->delete("/ootds/{$second->id}/wear");
        $this->assertNull($user->fresh()->wornToday());
    }

    public function test_cannot_favorite_or_wear_someone_elses_outfit(): void
    {
        $owner = User::factory()->create();
        $ootd = $this->outfit($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->postJson("/ootds/{$ootd->id}/favorite")->assertForbidden();
        $this->actingAs($intruder)->post("/ootds/{$ootd->id}/wear")->assertForbidden();
        $this->assertSame(0, $intruder->wearLogs()->count());
    }

    public function test_manage_page_filters_and_sorts(): void
    {
        $user = User::factory()->create();
        $this->outfit($user, ['description' => 'Blue polo', 'occasion' => 'work']);
        $dress = $this->outfit($user, ['description' => 'Red dress', 'occasion' => 'going-out']);
        $dress->forceFill(['is_favorite' => true])->save();

        $this->actingAs($user)->get('/ootds?q=polo')->assertSee('Blue polo')->assertDontSee('Red dress');
        $this->actingAs($user)->get('/ootds?occasion=going-out')->assertSee('Red dress')->assertDontSee('Blue polo');
        $this->actingAs($user)->get('/ootds?favorites=1')->assertSee('Red dress')->assertDontSee('Blue polo');
        $this->actingAs($user)->get('/ootds?sort=bogus&occasion=bogus')->assertOk()->assertSee('Blue polo')->assertSee('Red dress');
    }

    public function test_manage_page_can_sort_by_least_recently_worn(): void
    {
        $user = User::factory()->create();
        $worn = $this->outfit($user, ['description' => 'Worn one']);
        $this->outfit($user, ['description' => 'Never worn one']);
        $user->wearLogs()->create(['ootd_id' => $worn->id, 'worn_on' => today()->subDays(3)->toDateString()]);

        $this->actingAs($user)->get('/ootds?sort=least_recent')
            ->assertOk()
            ->assertSeeInOrder(['Never worn one', 'Worn one']);
    }

    public function test_picker_never_returns_another_users_or_excluded_outfit(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mine = $this->outfit($user);
        $this->outfit($other);
        $picker = new OutfitPicker;

        $this->assertSame($mine->id, $picker->pick($user)->id);
        $this->assertNull($picker->pick($user, excludeId: $mine->id));
        $this->assertNull($picker->pick($other, occasion: 'formal'));
        $this->assertNull($picker->pick(User::factory()->create()));
    }

    public function test_picker_prefers_outfits_not_worn_recently(): void
    {
        $user = User::factory()->create();
        $recent = $this->outfit($user);
        $stale = $this->outfit($user);
        $user->wearLogs()->create(['ootd_id' => $recent->id, 'worn_on' => today()->toDateString()]);
        $user->wearLogs()->create(['ootd_id' => $stale->id, 'worn_on' => today()->subDays(30)->toDateString()]);
        $picker = new OutfitPicker;

        // weights: worn today = 1, worn 30 days ago = 31. Rolls up to 1/32 land on the first.
        $this->assertSame($recent->id, $picker->pick($user, roll: 0.0)->id);
        $this->assertSame($stale->id, $picker->pick($user, roll: 0.5)->id);
        $this->assertSame($stale->id, $picker->pick($user, roll: 0.99)->id);
    }

    public function test_pick_page_filters_by_occasion_and_survives_shuffle_with_one_outfit(): void
    {
        $user = User::factory()->create();
        $only = $this->outfit($user, ['description' => 'The only one', 'occasion' => 'work']);

        $this->actingAs($user)->get('/pick')->assertOk()->assertSee('The only one');
        $this->actingAs($user)->get("/pick?occasion=work&skip={$only->id}")->assertOk()->assertSee('The only one');
        $this->actingAs($user)->get('/pick?occasion=formal')->assertOk()->assertSee('No outfits match');
    }

    public function test_history_lists_what_was_worn(): void
    {
        $user = User::factory()->create();
        $ootd = $this->outfit($user, ['description' => 'Friday fit']);

        $this->actingAs($user)->get('/history')->assertOk()->assertSee('Nothing logged yet');

        $user->wearLogs()->create(['ootd_id' => $ootd->id, 'worn_on' => today()->toDateString()]);

        $this->actingAs($user)->get('/history')->assertOk()->assertSee('Most worn')->assertSee(today()->format('F Y'));
    }

    public function test_dashboard_shows_todays_outfit(): void
    {
        $user = User::factory()->create();
        $ootd = $this->outfit($user, ['description' => 'Casual Friday']);

        $this->actingAs($user)->get('/dashboard')->assertSee('Suggested for you');

        $this->actingAs($user)->post("/ootds/{$ootd->id}/wear");

        $this->actingAs($user)->get('/dashboard')->assertSee('Wearing today')->assertSee('Casual Friday');
    }

    public function test_deleting_an_outfit_also_removes_its_history(): void
    {
        $user = User::factory()->create();
        $ootd = $this->outfit($user);
        $user->wearLogs()->create(['ootd_id' => $ootd->id, 'worn_on' => today()->toDateString()]);

        $this->actingAs($user)->delete("/ootds/{$ootd->id}");

        $this->assertSame(0, $user->wearLogs()->count());
    }
}
