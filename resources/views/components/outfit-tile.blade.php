@props(['ootd', 'todayId' => null])

<article class="card wardrobe-card group flex flex-col overflow-hidden">
    <a href="{{ $ootd->imageUrl() }}" data-lightbox data-caption="{{ $ootd->altText() }}" class="block overflow-hidden bg-stone-100 dark:bg-stone-800">
        <img src="{{ $ootd->imageUrl() }}" alt="{{ $ootd->altText() }}" loading="lazy"
             class="aspect-[3/4] w-full object-cover transition duration-300 group-hover:scale-105">
    </a>
    <div class="flex flex-1 flex-col p-3">
        <p @class(['truncate text-sm', 'italic text-stone-400 dark:text-stone-500' => ! $ootd->description])>{{ $ootd->description ?: 'No description' }}</p>
        @if ($summary = $ootd->wearSummary())
            <p class="mt-0.5 truncate text-xs text-stone-500 dark:text-stone-400">{{ $summary }}</p>
        @endif
        <x-wear-button :ootd="$ootd" :today="$todayId === $ootd->id" class="mt-3" />
    </div>
</article>
