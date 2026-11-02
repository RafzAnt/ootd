<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_login_from_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/ootds')->assertRedirect(route('login'));
    }

    public function test_user_can_sign_up(): void
    {
        $response = $this->post('/register', [
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
    }

    public function test_email_is_case_insensitive(): void
    {
        $this->post('/register', [
            'name' => 'Juan',
            'email' => ' Juan@Example.COM ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));
        $this->post('/logout');

        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);

        $this->post('/login', ['email' => 'JUAN@example.com', 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_sign_up_requires_matching_password_and_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/register', [
            'name' => 'Juan',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_user_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logged_in_users_skip_the_landing_and_auth_pages(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/')->assertRedirect(route('dashboard'));
        $this->get('/login')->assertRedirect(route('dashboard'));
    }

    public function test_every_sidebar_page_renders_for_a_logged_in_user(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Maria Clara']));

        $items = collect(config('dashboard.nav'))->flatMap(fn ($section) => $section['items']);

        $this->assertNotEmpty($items);

        foreach ($items as $item) {
            $this->get(route($item['route']))
                ->assertOk()
                ->assertSee($item['label']);
        }
    }
}
