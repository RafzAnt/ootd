@forelse ($notifications as $notification)
    <x-notification-item :notification="$notification" />
@empty
    <div class="px-6 py-10 text-center">
        <span class="mx-auto grid size-11 place-items-center rounded-full bg-stone-100 text-stone-400 dark:bg-stone-800">
            <x-icon name="bell" class="size-5" />
        </span>
        <p class="mt-3 text-sm font-medium">You're all caught up</p>
        <p class="mt-0.5 text-sm text-stone-500 dark:text-stone-400">Reminders and updates will show up here.</p>
    </div>
@endforelse
