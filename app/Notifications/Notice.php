<?php

namespace App\Notifications;

use App\Models\Ootd;
use Illuminate\Notifications\Notification;

/**
 * The one in-app notification the app sends. To announce something new, call
 * `$user->notify(new Notice(...))` or add a named constructor below.
 *
 * `kind` only picks the icon/colour in the UI: welcome, reminder, milestone,
 * security or info (see resources/views/components/notification-icon.blade.php).
 */
class Notice extends Notification
{
    public function __construct(
        public string $kind,
        public string $title,
        public string $body,
        public ?string $url = null,
        public ?string $image = null,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'image' => $this->image,
        ];
    }

    public static function welcome(string $name): self
    {
        return new self(
            'welcome',
            'Welcome, '.strtok($name, ' ').'!',
            'Open Upload OOTD from the menu to add a few favorites, then we\'ll help you pick one in seconds.',
            route('dashboard', absolute: false),
        );
    }

    public static function milestone(int $count): self
    {
        return new self(
            'milestone',
            $count === 1 ? 'First outfit saved' : "{$count} outfits saved",
            $count === 1
                ? 'Your lookbook has started. Add a few more and "Pick for me" gets even better.'
                : "Nice! You've built a lookbook of {$count} outfits.",
            route('ootds.index', absolute: false),
        );
    }

    public static function reminder(Ootd $ootd): self
    {
        return new self(
            'reminder',
            'What to wear today?',
            'How about this one? Tap to see it, or shuffle for another.',
            route('pick', absolute: false),
            $ootd->image_path,
        );
    }

    public static function passwordChanged(): self
    {
        return new self(
            'security',
            'Your password was changed',
            'If this wasn\'t you, change your password again right away.',
            route('account.edit', absolute: false),
        );
    }

    public static function test(): self
    {
        return new self(
            'info',
            'Test notification',
            'Notifications are working. You\'ll see reminders and updates here.',
            route('notifications.index', absolute: false),
        );
    }
}
