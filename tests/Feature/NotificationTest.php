<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\Notice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_signing_up_sends_a_welcome_notification(): void
    {
        $this->post('/register', [
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::firstWhere('email', 'juan@example.com');

        $this->assertSame(1, $user->unreadNotifications()->count());
        $this->assertSame('Welcome, Juan!', $user->notifications()->first()->data['title']);
    }

    public function test_uploading_outfits_hits_milestones(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $photo = fn () => UploadedFile::fake()->createWithContent('fit.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        $this->actingAs($user)->post('/ootds', ['photo' => $photo()]);
        $this->assertSame(['First outfit saved'], $user->notifications->pluck('data.title')->all());

        // 2, 3 and 4 outfits: no notification. The 5th: milestone.
        foreach (range(1, 4) as $i) {
            $this->actingAs($user)->post('/ootds', ['photo' => $photo()]);
        }

        // Both were created within the same second, so don't depend on their order.
        $this->assertEqualsCanonicalizing(
            ['5 outfits saved', 'First outfit saved'],
            $user->fresh()->notifications->pluck('data.title')->all(),
        );
    }

    public function test_changing_password_sends_a_security_notice(): void
    {
        $user = User::factory()->create(['password' => 'oldpassword']);

        $this->actingAs($user)->put('/account/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ]);

        $this->assertSame('security', $user->notifications()->first()->data['kind']);
    }

    public function test_bell_and_page_show_notifications(): void
    {
        $user = User::factory()->create();
        $user->notify(new Notice('info', 'Hello there', 'Some body text'));

        $this->actingAs($user)->get('/dashboard')->assertSee('Hello there');
        $this->actingAs($user)->get('/notifications')->assertOk()->assertSee('Hello there')->assertSee('1 unread');
    }

    public function test_summary_endpoint_returns_unread_count_and_html(): void
    {
        $user = User::factory()->create();
        $user->notify(new Notice('info', 'Ping', 'Body'));

        $this->actingAs($user)->getJson('/notifications/summary')
            ->assertOk()
            ->assertJson(['unread' => 1])
            ->assertJsonPath('html', fn ($html) => str_contains($html, 'Ping'));
    }

    public function test_opening_a_notification_marks_it_read_and_follows_its_link(): void
    {
        $user = User::factory()->create();
        $user->notify(new Notice('info', 'Go', 'Body', '/pick'));
        $notification = $user->notifications()->first();

        $this->actingAs($user)->get("/notifications/{$notification->id}")->assertRedirect('/pick');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_notification_links_cannot_leave_the_app(): void
    {
        $user = User::factory()->create();
        $user->notify(new Notice('info', 'Evil', 'Body', '//evil.example.com'));
        $user->notify(new Notice('info', 'Evil 2', 'Body', 'https://evil.example.com'));

        foreach ($user->notifications as $notification) {
            $this->actingAs($user)->get("/notifications/{$notification->id}")->assertRedirect(route('notifications.index'));
        }
    }

    public function test_read_all_delete_and_clear(): void
    {
        $user = User::factory()->create();
        $user->notify(new Notice('info', 'One', 'Body'));
        $user->notify(new Notice('info', 'Two', 'Body'));

        $this->actingAs($user)->post('/notifications/read-all');
        $this->assertSame(0, $user->unreadNotifications()->count());

        $this->actingAs($user)->delete('/notifications/'.$user->notifications()->first()->id);
        $this->assertSame(1, $user->notifications()->count());

        $this->actingAs($user)->delete('/notifications');
        $this->assertSame(0, $user->notifications()->count());
    }

    public function test_users_cannot_open_or_delete_each_others_notifications(): void
    {
        $owner = User::factory()->create();
        $owner->notify(new Notice('info', 'Private', 'Body'));
        $id = $owner->notifications()->first()->id;
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get("/notifications/{$id}")->assertNotFound();
        $this->actingAs($intruder)->delete("/notifications/{$id}")->assertNotFound();
        $this->assertSame(1, $owner->notifications()->count());
    }

    public function test_reminder_settings_can_be_saved_and_test_notification_sent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/settings/notifications', ['reminder_time' => '07:30'])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertFalse($user->reminders_enabled);
        $this->assertSame('07:30', $user->reminder_time);

        $this->actingAs($user)->put('/settings/notifications', ['reminders_enabled' => '1', 'reminder_time' => 'nonsense'])
            ->assertSessionHasErrors('reminder_time');

        $this->actingAs($user)->post('/settings/notifications/test');
        $this->assertSame('Test notification', $user->notifications()->first()->data['title']);
    }

    public function test_reminder_command_respects_time_and_sends_once_a_day(): void
    {
        $this->travelTo(now()->setTime(12, 0));
        $user = User::factory()->create(['reminder_time' => '09:00']);
        $user->ootds()->create(['image_path' => 'ootds/1/a.jpg']);
        $early = User::factory()->create(['reminder_time' => '18:00']);
        $early->ootds()->create(['image_path' => 'ootds/2/a.jpg']);

        $this->artisan('ootd:send-reminders')->expectsOutputToContain('Sent 1 reminder')->assertSuccessful();
        $this->artisan('ootd:send-reminders')->expectsOutputToContain('Sent 0 reminder');

        $this->assertSame('reminder', $user->notifications()->first()->data['kind']);
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame(0, $early->notifications()->count());
    }

    public function test_reminder_command_skips_disabled_empty_and_already_decided_users(): void
    {
        $this->travelTo(now()->setTime(12, 0));

        $disabled = User::factory()->create(['reminder_time' => '09:00', 'reminders_enabled' => false]);
        $disabled->ootds()->create(['image_path' => 'ootds/1/a.jpg']);

        $empty = User::factory()->create(['reminder_time' => '09:00']);

        $decided = User::factory()->create(['reminder_time' => '09:00']);
        $ootd = $decided->ootds()->create(['image_path' => 'ootds/3/a.jpg']);
        $decided->wearLogs()->create(['ootd_id' => $ootd->id, 'worn_on' => today()->toDateString()]);

        $this->artisan('ootd:send-reminders')->expectsOutputToContain('Sent 0 reminder');

        foreach ([$disabled, $empty, $decided] as $user) {
            $this->assertSame(0, $user->notifications()->count());
        }
    }
}
