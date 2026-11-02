@props(['ootd'])

<form method="POST" action="{{ route('ootds.favorite', $ootd) }}" data-favorite {{ $attributes }}>
    @csrf
    <button type="submit"
            aria-pressed="{{ $ootd->is_favorite ? 'true' : 'false' }}"
            aria-label="{{ $ootd->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"
            class="grid size-9 cursor-pointer place-items-center rounded-full bg-white/90 text-stone-500 shadow-sm ring-1 ring-black/5 backdrop-blur transition hover:scale-110 hover:text-rose-500 aria-pressed:text-rose-500 dark:bg-stone-900/90 dark:text-stone-300">
        <x-icon name="heart" @class(['size-5', 'fill-current' => $ootd->is_favorite]) />
    </button>
</form>
