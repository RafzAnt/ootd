<x-layouts.app title="Notifications" :subtitle="$unread > 0 ? $unread.' unread' : 'You\'re all caught up.'">
    @if ($notifications->isNotEmpty())
        <x-slot:actions>
            <div class="flex items-center gap-2">
                @if ($unread > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn-secondary"><x-icon name="check" class="size-4" /> Mark all as read</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('notifications.clear') }}" data-confirm="Delete all notifications?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary"><x-icon name="trash" class="size-4" /> Clear all</button>
                </form>
            </div>
        </x-slot:actions>
    @endif

    <div class="card max-w-3xl divide-y divide-stone-100 overflow-hidden dark:divide-stone-800">
        @forelse ($notifications as $notification)
            <x-notification-item :notification="$notification" deletable />
        @empty
            @include('notifications._list', ['notifications' => $notifications])
        @endforelse
    </div>

    <div class="mt-6 max-w-3xl">{{ $notifications->links() }}</div>
</x-layouts.app>
