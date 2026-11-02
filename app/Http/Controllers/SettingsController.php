<?php

namespace App\Http\Controllers;

use App\Notifications\Notice;
use App\Services\OutfitImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.edit', ['user' => $request->user()]);
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reminder_time' => ['required', 'date_format:H:i'],
        ]);

        $request->user()->update([
            'reminders_enabled' => $request->boolean('reminders_enabled'),
            'reminder_time' => $data['reminder_time'],
        ]);

        return back()->with('status', 'Notification settings saved.');
    }

    public function testNotification(Request $request): RedirectResponse
    {
        $request->user()->notify(Notice::test());

        return back()->with('status', 'Test notification sent — check the bell.');
    }

    public function destroy(Request $request, OutfitImageStorage $imageStorage): RedirectResponse
    {
        $request->validateWithBag('deletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $imageStorage->deleteMany($user->ootds()->pluck('image_path')->all());
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
