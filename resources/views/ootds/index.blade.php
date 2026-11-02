<x-layouts.app title="Manage OOTD" :subtitle="$total === 0 ? 'All your saved outfits in one place.' : $total.' saved '.Str::plural('outfit', $total).'.'">
    @if ($total === 0)
        <div class="card empty-state flex flex-col items-center px-6 py-16 text-center">
            <span class="grid size-14 place-items-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">
                <x-icon name="image" class="size-7" />
            </span>
            <h2 class="mt-5 text-lg font-semibold">Nothing here yet</h2>
            <p class="mt-1 max-w-sm text-sm text-stone-500 dark:text-stone-400">No OOTD saved yet. Open <strong>Upload OOTD</strong> from the menu to add your first outfit.</p>
        </div>
    @else
        <form method="GET" action="{{ route('ootds.index') }}" data-autosubmit class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-white/80 bg-white/65 p-3 shadow-sm shadow-stone-900/[0.03] dark:border-stone-800 dark:bg-stone-900/65">
            <div class="relative min-w-52 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search descriptions" aria-label="Search descriptions" class="field pl-10">
            </div>

            <select name="occasion" aria-label="Occasion" class="field w-auto">
                <option value="">All occasions</option>
                @foreach (config('ootd.occasions') as $key => $label)
                    <option value="{{ $key }}" @selected($filters['occasion'] === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="sort" aria-label="Sort by" class="field w-auto">
                @foreach ($sorts as $key => $label)
                    <option value="{{ $key }}" @selected($filters['sort'] === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <label class="cursor-pointer">
                <input type="checkbox" name="favorites" value="1" @checked($filters['favorites']) class="sr-only">
                <span class="chip"><x-icon name="heart" class="size-4" /> Favorites</span>
            </label>

            @if ($isFiltered || $filters['sort'] !== 'newest')
                <a href="{{ route('ootds.index') }}" class="text-sm font-semibold text-rose-600 hover:underline">Clear</a>
            @endif
        </form>

        @if ($ootds->isEmpty())
            <div class="card flex flex-col items-center px-6 py-14 text-center">
                <span class="grid size-12 place-items-center rounded-2xl bg-stone-100 text-stone-400 dark:bg-stone-800">
                    <x-icon name="search" class="size-6" />
                </span>
                <h2 class="mt-4 font-semibold">No outfits match</h2>
                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Try a different search or filter.</p>
                <a href="{{ route('ootds.index') }}" class="btn-secondary mt-5">Clear filters</a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                @foreach ($ootds as $ootd)
                    <article class="card wardrobe-card fade-up group flex flex-col overflow-hidden">
                        <div class="relative overflow-hidden bg-stone-100 dark:bg-stone-800">
                            <a href="{{ $ootd->imageUrl() }}" data-lightbox data-caption="{{ $ootd->altText() }}" class="block">
                                <img src="{{ $ootd->imageUrl() }}" alt="{{ $ootd->altText() }}" loading="lazy"
                                     class="aspect-[3/4] w-full object-cover transition duration-300 group-hover:scale-105">
                            </a>

                            <x-favorite-button :ootd="$ootd" class="absolute right-2.5 top-2.5" />

                            @if ($wornTodayId === $ootd->id)
                                <span class="absolute left-2.5 top-2.5 inline-flex items-center gap-1 rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
                                    <x-icon name="check" class="size-3" /> Today
                                </span>
                            @endif

                            @if ($ootd->occasionLabel())
                                <span class="pointer-events-none absolute bottom-2.5 left-2.5 rounded-full bg-black/60 px-2.5 py-1 text-xs font-medium text-white backdrop-blur">{{ $ootd->occasionLabel() }}</span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-3.5">
                            <p @class(['line-clamp-2 text-sm', 'italic text-stone-400 dark:text-stone-500' => ! $ootd->description])>
                                {{ $ootd->description ?: 'No description' }}
                            </p>
                            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">{{ $ootd->wearSummary() }}</p>

                            <div class="mt-auto pt-3">
                                <x-wear-button :ootd="$ootd" :today="$wornTodayId === $ootd->id" />

                                <div class="mt-2.5 flex items-center justify-between text-xs font-medium">
                                    <a href="{{ route('ootds.edit', $ootd) }}"
                                       class="inline-flex items-center gap-1.5 rounded-md py-1 text-stone-500 transition hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100">
                                        <x-icon name="pencil" class="size-3.5" /> Edit
                                    </a>

                                    <form method="POST" action="{{ route('ootds.destroy', $ootd) }}" data-confirm="Delete this outfit? This can't be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex cursor-pointer items-center gap-1.5 rounded-md py-1 text-stone-500 transition hover:text-red-600 dark:text-stone-400 dark:hover:text-red-400">
                                            <x-icon name="trash" class="size-3.5" /> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">{{ $ootds->links() }}</div>
        @endif
    @endif
</x-layouts.app>
