<x-layouts.app title="History" subtitle="What you've worn, so you don't repeat yourself by accident.">
    @if ($logs->isEmpty())
        <div class="card empty-state flex flex-col items-center px-6 py-16 text-center">
            <span class="grid size-14 place-items-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">
                <x-icon name="clock" class="size-7" />
            </span>
            <h2 class="mt-5 text-lg font-semibold">Nothing logged yet</h2>
            <p class="mt-1 max-w-sm text-sm text-stone-500 dark:text-stone-400">
                @if ($hasOutfits)
                    Tap "Wear today" on an outfit and it will show up here.
                @else
                    No OOTD saved yet. Open <strong>Upload OOTD</strong> from the menu to add your first outfit.
                @endif
            </p>
            @if ($hasOutfits)
                <a href="{{ route('pick') }}" class="btn-primary mt-6"><x-icon name="dice" class="size-4" /> Pick for me</a>
            @endif
        </div>
    @else
        @if ($mostWorn->isNotEmpty())
            <section class="mb-10">
                <h2 class="mb-4 text-lg font-semibold">Most worn</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($mostWorn as $ootd)
                        <a href="{{ $ootd->imageUrl() }}" data-lightbox data-caption="{{ $ootd->altText() }}" class="card flex items-center gap-4 p-3 transition hover:border-rose-300 dark:hover:border-rose-500/50">
                            <img src="{{ $ootd->imageUrl() }}" alt="{{ $ootd->altText() }}" class="size-16 rounded-xl object-cover">
                            <div class="min-w-0">
                                <p class="text-2xl font-semibold leading-none">{{ $ootd->wear_logs_count }}×</p>
                                <p class="mt-1 truncate text-sm text-stone-500 dark:text-stone-400">{{ $ootd->description ?: 'No description' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @foreach ($months as $month => $entries)
            <section class="mb-10">
                <h2 class="mb-4 text-lg font-semibold">{{ $month }}</h2>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6">
                    @foreach ($entries as $log)
                        <figure>
                            <a href="{{ $log->ootd->imageUrl() }}" data-lightbox data-caption="{{ $log->worn_on->format('l, F j') }}{{ $log->ootd->description ? ' — '.$log->ootd->description : '' }}"
                               class="block overflow-hidden rounded-xl bg-stone-100 dark:bg-stone-800">
                                <img src="{{ $log->ootd->imageUrl() }}" alt="{{ $log->ootd->altText() }}" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-300 hover:scale-105">
                            </a>
                            <figcaption class="mt-1.5 text-center text-xs font-medium text-stone-500 dark:text-stone-400">
                                {{ $log->worn_on->isToday() ? 'Today' : ($log->worn_on->isYesterday() ? 'Yesterday' : $log->worn_on->format('M j')) }}
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{ $logs->links() }}
    @endif
</x-layouts.app>
