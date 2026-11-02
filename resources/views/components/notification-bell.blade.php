<div class="relative" data-dropdown data-bell data-summary-url="{{ route('notifications.summary') }}" data-unread="{{ $unread }}">
    <button type="button" data-dropdown-trigger aria-haspopup="true" aria-expanded="false" aria-label="Notifications"
            class="relative grid size-10 cursor-pointer place-items-center rounded-xl text-stone-600 transition hover:bg-stone-100 dark:text-stone-300 dark:hover:bg-stone-800">
        <x-icon name="bell" class="size-5.5" />
        <span data-bell-badge @class(['absolute right-0 top-0 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-rose-600 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white dark:ring-stone-900', 'hidden' => $unread === 0])>{{ $unread > 9 ? '9+' : $unread }}</span>
    </button>

    <div data-dropdown-panel hidden
         class="dropdown-in absolute right-0 z-40 mt-2 w-88 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl shadow-stone-900/10 dark:border-stone-700 dark:bg-stone-900">
        <div class="flex items-center justify-between border-b border-stone-200 px-4 py-3 dark:border-stone-800">
            <h2 class="text-sm font-semibold">Notifications</h2>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="cursor-pointer text-xs font-semibold text-rose-600 hover:underline">Mark all as read</button>
            </form>
        </div>

        <div data-bell-list class="max-h-96 divide-y divide-stone-100 overflow-y-auto dark:divide-stone-800">
            @include('notifications._list', ['notifications' => $notifications])
        </div>

        <a href="{{ route('notifications.index') }}" class="block border-t border-stone-200 px-4 py-3 text-center text-sm font-semibold text-stone-600 transition hover:bg-stone-50 dark:border-stone-800 dark:text-stone-300 dark:hover:bg-stone-800/60">
            See all notifications
        </a>
    </div>
</div>
