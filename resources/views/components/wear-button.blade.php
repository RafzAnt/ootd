@props(['ootd', 'today' => false, 'then' => null])

@if ($today)
    <form method="POST" action="{{ route('ootds.unwear', $ootd) }}" {{ $attributes->only('class') }}>
        @csrf
        @method('DELETE')
        <button type="submit" title="Click to undo"
                class="inline-flex w-full cursor-pointer items-center whitespace-nowrap justify-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 transition hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">
            <x-icon name="check" class="size-4" /> Wearing today
        </button>
    </form>
@else
    <form method="POST" action="{{ route('ootds.wear', $ootd) }}" {{ $attributes->only('class') }}>
        @csrf
        @if ($then) <input type="hidden" name="then" value="{{ $then }}"> @endif
        <button type="submit" class="btn-secondary w-full whitespace-nowrap px-3 py-2">
            <x-icon name="shirt" class="size-4" /> Wear today
        </button>
    </form>
@endif
