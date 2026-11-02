<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OotdTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function photo(string $name = 'fit.jpg'): UploadedFile
    {
        // A 1x1 PNG; Laravel's `image` rule only checks the mime type.
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));
    }

    public function test_user_can_upload_an_ootd_without_a_description(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/ootds', ['photo' => $this->photo('fit.png')])
            ->assertRedirect(route('ootds.index'));

        $ootd = $user->ootds()->first();
        $this->assertNotNull($ootd);
        $this->assertNull($ootd->description);
        Storage::disk('public')->assertExists($ootd->image_path);
    }

    public function test_user_can_upload_an_ootd_with_a_description(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/ootds', ['photo' => $this->photo('fit.png'), 'description' => 'Black polo + jeans']);

        $this->assertSame('Black polo + jeans', $user->ootds()->first()->description);
    }

    public function test_upload_requires_a_real_image(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/ootds', [])->assertSessionHasErrors('photo');
        $this->actingAs($user)
            ->post('/ootds', ['photo' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('photo');

        $this->assertSame(0, $user->ootds()->count());
    }

    public function test_manage_page_only_lists_the_users_own_outfits(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $me->ootds()->create(['image_path' => 'ootds/1/mine.jpg', 'description' => 'My fit']);
        $other->ootds()->create(['image_path' => 'ootds/2/theirs.jpg', 'description' => 'Their fit']);

        $this->actingAs($me)->get('/ootds')
            ->assertOk()
            ->assertSee('My fit')
            ->assertDontSee('Their fit');
    }

    public function test_user_can_edit_description_and_replace_photo(): void
    {
        $user = User::factory()->create();
        $old = $this->photo('old.png')->store('ootds/'.$user->id, 'public');
        $ootd = $user->ootds()->create(['image_path' => $old, 'description' => 'Old']);

        $this->actingAs($user)
            ->put("/ootds/{$ootd->id}", ['description' => '', 'photo' => $this->photo('new.png')])
            ->assertRedirect(route('ootds.index'));

        $ootd->refresh();
        $this->assertNull($ootd->description);
        $this->assertNotSame($old, $ootd->image_path);
        Storage::disk('public')->assertExists($ootd->image_path);
        Storage::disk('public')->assertMissing($old);
    }

    public function test_deleting_an_ootd_removes_its_file(): void
    {
        $user = User::factory()->create();
        $path = $this->photo()->store('ootds/'.$user->id, 'public');
        $ootd = $user->ootds()->create(['image_path' => $path]);

        $this->actingAs($user)->delete("/ootds/{$ootd->id}")->assertRedirect(route('ootds.index'));

        $this->assertModelMissing($ootd);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_users_cannot_touch_other_peoples_outfits(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $ootd = $owner->ootds()->create(['image_path' => 'ootds/1/a.jpg', 'description' => 'Private']);

        $this->actingAs($intruder)->get("/ootds/{$ootd->id}/edit")->assertForbidden();
        $this->actingAs($intruder)->put("/ootds/{$ootd->id}", ['description' => 'Hacked'])->assertForbidden();
        $this->actingAs($intruder)->delete("/ootds/{$ootd->id}")->assertForbidden();

        $this->assertSame('Private', $ootd->fresh()->description);
    }
}
