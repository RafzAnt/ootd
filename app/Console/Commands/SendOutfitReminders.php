<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Notice;
use App\Services\OutfitPicker;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('ootd:send-reminders')]
#[Description('Send the daily "what to wear" reminder to users whose reminder time has passed')]
class SendOutfitReminders extends Command
{
    public function handle(OutfitPicker $picker): int
    {
        $sent = 0;

        User::query()
            ->where('reminders_enabled', true)
            ->where('reminder_time', '<=', now()->format('H:i'))
            ->where(fn ($query) => $query
                ->whereNull('last_reminder_on')
                ->orWhere('last_reminder_on', '<', today()->toDateString()))
            ->chunkById(100, function ($users) use ($picker, &$sent) {
                foreach ($users as $user) {
                    // Already decided today, or nothing to suggest: nothing to remind about.
                    $suggestion = $user->wornToday() ? null : $picker->pick($user);

                    if ($suggestion) {
                        $user->notify(Notice::reminder($suggestion));
                        $sent++;
                    }

                    if ($suggestion || $user->ootds()->exists()) {
                        $user->forceFill(['last_reminder_on' => today()])->save();
                    }
                }
            });

        $this->info("Sent {$sent} reminder(s).");

        return self::SUCCESS;
    }
}
