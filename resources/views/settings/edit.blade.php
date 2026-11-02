<x-layouts.app title="Settings" subtitle="Make the app yours.">
    <div class="max-w-xl space-y-6">
        <section class="card p-6">
            <h2 class="font-semibold">Appearance</h2>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Saved on this device only.</p>

            <div class="mt-4 grid grid-cols-3 gap-3">
                @foreach (['light' => 'Light', 'dark' => 'Dark', 'system' => 'System'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="theme" value="{{ $value }}" class="peer sr-only">
                        <span class="block rounded-lg border border-stone-300 px-3 py-2.5 text-center text-sm font-medium transition peer-checked:border-rose-600 peer-checked:bg-rose-50 peer-checked:text-rose-700 peer-focus-visible:ring-2 peer-focus-visible:ring-rose-500/50 dark:border-stone-700 dark:peer-checked:bg-rose-500/10 dark:peer-checked:text-rose-300">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="card p-6">
            <h2 class="font-semibold">Notifications</h2>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Reminders show up under the bell at the top.</p>

            <form method="POST" action="{{ route('settings.notifications') }}" class="mt-5 space-y-5">
                @csrf
                @method('PUT')

                <label class="flex cursor-pointer items-center justify-between gap-4">
                    <span>
                        <span class="block text-sm font-medium">Daily reminder</span>
                        <span class="block text-sm text-stone-500 dark:text-stone-400">A "what to wear?" nudge with a suggestion. Skipped on days you've already picked.</span>
                    </span>
                    <input type="checkbox" name="reminders_enabled" value="1" @checked($user->reminders_enabled) class="peer sr-only">
                    <span class="relative h-6 w-11 shrink-0 rounded-full bg-stone-300 transition peer-checked:bg-rose-600 peer-focus-visible:ring-2 peer-focus-visible:ring-rose-500/50 after:absolute after:left-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5 dark:bg-stone-700"></span>
                </label>

                <div>
                    <label for="reminder_time" class="label">Remind me at</label>
                    <input id="reminder_time" name="reminder_time" type="time" value="{{ old('reminder_time', $user->reminder_time) }}" required class="field w-40">
                    <p class="mt-1.5 text-xs text-stone-500 dark:text-stone-400">Pick a time that fits your shift ({{ config('app.timezone') }} time).</p>
                    @error('reminder_time') <p class="error-text">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary">Save</button>
            </form>

            <form method="POST" action="{{ route('settings.notifications.test') }}" class="mt-4 border-t border-stone-100 pt-4 dark:border-stone-800">
                @csrf
                <button type="submit" class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-rose-600 hover:underline">
                    <x-icon name="bell" class="size-4" /> Send me a test notification
                </button>
            </form>
        </section>

        <section class="card border-red-200 p-6 dark:border-red-500/30">
            <h2 class="font-semibold text-red-700 dark:text-red-400">Delete account</h2>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                This permanently deletes your account and all of your uploaded outfits.
            </p>

            <form method="POST" action="{{ route('settings.destroy') }}" data-confirm="Delete your account and all your outfits? This can't be undone." class="mt-4 space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="delete_password" class="label">Confirm with your password</label>
                    <input id="delete_password" name="password" type="password" required autocomplete="current-password" class="field">
                    @error('password', 'deletion') <p class="error-text">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-danger">Delete my account</button>
            </form>
        </section>
    </div>
</x-layouts.app>
