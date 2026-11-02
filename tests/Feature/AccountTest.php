<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/account', ['name' => 'New Name', 'email' => 'New@Example.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('New Name', $user->fresh()->name);
        $this->assertSame('new@example.com', $user->fresh()->email);
    }

    public function test_profile_email_must_stay_unique(): void
    {
        $taken = User::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/account', ['name' => $user->name, 'email' => $taken->email])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_keep_their_own_email(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/account', ['name' => 'Renamed', 'email' => $user->email])
            ->assertSessionHasNoErrors();
    }

    public function test_user_can_change_password_with_the_current_one(): void
    {
        $user = User::factory()->create(['password' => 'oldpassword']);

        $this->actingAs($user)->put('/account/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
    }

    public function test_password_change_rejects_wrong_current_password(): void
    {
        $user = User::factory()->create(['password' => 'oldpassword']);

        $this->actingAs($user)->put('/account/password', [
            'current_password' => 'nope',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertSessionHasErrorsIn('password', 'current_password');

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function test_user_can_delete_their_account_and_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['password' => 'secret-pass']);
        Storage::disk('public')->put("ootds/{$user->id}/a.jpg", 'x');
        $user->ootds()->create(['image_path' => "ootds/{$user->id}/a.jpg"]);

        $this->actingAs($user)
            ->delete('/settings/account', ['password' => 'secret-pass'])
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertModelMissing($user);
        $this->assertDatabaseCount('ootds', 0);
        Storage::disk('public')->assertMissing("ootds/{$user->id}/a.jpg");
    }

    public function test_account_deletion_needs_the_right_password(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass']);

        $this->actingAs($user)
            ->delete('/settings/account', ['password' => 'wrong'])
            ->assertSessionHasErrorsIn('deletion', 'password');

        $this->assertModelExists($user);
    }
}
