@props(['notification', 'deletable' => false])

@php($data = $notification->data)

<div @class([
    'group relative flex items-start gap-3 px-4 py-3.5 transition hover:bg-stone-50 dark:hover:bg-stone-800/60',
    'bg-rose-50/50 dark:bg-rose-500/5' => $notification->unread(),
])>
    <a href="{{ route('notifications.show', $notification->id) }}" class="absolute inset-0 z-0" aria-label="{{ $data['title'] }}"></a>

    <x-notification-icon :kind="$data['kind'] ?? 'info'" />

    <div class="min-w-0 flex-1">
        <p @class(['text-sm', 'font-semibold' => $notification->unread(), 'font-medium' => ! $notification->unread()])>{{ $data['title'] }}</p>
        <p class="mt-0.5 line-clamp-2 text-sm text-stone-500 dark:text-stone-400">{{ $data['body'] }}</p>
        <p class="mt-1 text-xs text-stone-400 dark:text-stone-500">{{ $notification->created_at->diffForHumans() }}</p>
    </div>

    @if (! empty($data['image']))
        <img src="{{ asset('storage/'.$data['image']) }}" alt="" onerror="this.remove()" class="size-12 shrink-0 rounded-lg object-cover">
    @endif

    @if ($notification->unread())
        <span class="mt-1.5 size-2 shrink-0 rounded-full bg-rose-500" title="Unread"></span>
    @endif

    @if ($deletable)
        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="relative z-10 -mr-1.5 shrink-0">
            @csrf
            @method('DELETE')
            <button type="submit" aria-label="Delete notification"
                    class="grid size-8 cursor-pointer place-items-center rounded-lg text-stone-400 transition hover:bg-stone-200 hover:text-stone-700 dark:hover:bg-stone-700 dark:hover:text-stone-200">
                <x-icon name="x" class="size-4" />
            </button>
        </form>
    @endif
</div>
