<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationBell extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        return view('components.notification-bell', [
            'unread' => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()->limit(6)->get(),
        ]);
    }
}
