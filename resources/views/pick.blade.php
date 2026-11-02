<x-layouts.app title="Pick for me" subtitle="Can't decide? Here's a suggestion — mostly things you haven't worn lately.">
    @if ($total === 0)
        <div class="card empty-state flex flex-col items-center px-6 py-16 text-center">
            <span class="grid size-14 place-items-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">
                <x-icon name="dice" class="size-7" />
            </span>
            <h2 class="mt-5 text-lg font-semibold">Nothing to pick from yet</h2>
            <p class="mt-1 max-w-sm text-sm text-stone-500 dark:text-stone-400">No OOTD saved yet. Open <strong>Upload OOTD</strong> from the menu, then come back and we'll choose for you.</p>
        </div>
    @else
        @if ($occasions->isNotEmpty())
            <div class="mb-6 flex flex-wrap gap-2" aria-label="Occasion">
                <a href="{{ route('pick') }}" class="chip" aria-current="{{ $occasion === null ? 'true' : 'false' }}">Any</a>
                @foreach ($occasions as $key => $label)
                    <a href="{{ route('pick', ['occasion' => $key]) }}" class="chip" aria-current="{{ $occasion === $key ? 'true' : 'false' }}">{{ $label }}</a>
                @endforeach
            </div>
        @endif

        @if ($wornToday)
            <div class="mb-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                <img src="{{ $wornToday->imageUrl() }}" alt="" class="size-10 rounded-lg object-cover">
                <p>You're already wearing an outfit today. Choosing another one will replace it.</p>
            </div>
        @endif

        @if ($ootd)
            <div class="card fade-up grid overflow-hidden md:grid-cols-[minmax(0,22rem)_1fr]">
                <a href="{{ $ootd->imageUrl() }}" data-lightbox data-caption="{{ $ootd->altText() }}" class="block bg-stone-100 dark:bg-stone-800">
                    <img src="{{ $ootd->imageUrl() }}" alt="{{ $ootd->altText() }}" class="aspect-[3/4] max-h-[30rem] size-full object-cover object-top md:aspect-auto md:max-h-none">
                </a>

                <div class="flex flex-col p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">
                            <x-icon name="dice" class="size-3.5" /> Today's pick
                        </span>
                        <x-favorite-button :ootd="$ootd" />
                    </div>

                    <h2 @class(['mt-5 text-2xl font-semibold tracking-tight', 'text-stone-400 dark:text-stone-500' => ! $ootd->description])>
                        {{ $ootd->description ?: 'No description' }}
                    </h2>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if ($ootd->occasionLabel()) <span class="pill">{{ $ootd->occasionLabel() }}</span> @endif
                        <span class="text-sm text-stone-500 dark:text-stone-400">{{ $ootd->wearSummary() }}</span>
                    </div>

                    <div class="mt-auto flex flex-wrap items-center gap-3 pt-8">
                        <form method="POST" action="{{ route('ootds.wear', $ootd) }}">
                            @csrf
                            <input type="hidden" name="then" value="dashboard">
                            <button type="submit" class="btn-primary px-5 py-3"><x-icon name="shirt" class="size-4" /> Wear this today</button>
                        </form>
                        <a href="{{ route('pick', array_filter(['occasion' => $occasion, 'skip' => $ootd->id])) }}" class="btn-secondary px-5 py-3">
                            <x-icon name="refresh" class="size-4" /> Shuffle
                        </a>
                        <a href="{{ route('ootds.edit', $ootd) }}" class="text-sm font-semibold text-stone-500 hover:text-stone-800 dark:text-stone-400 dark:hover:text-stone-200">Edit</a>
                    </div>
                </div>
            </div>
        @else
            <div class="card empty-state flex flex-col items-center px-6 py-16 text-center">
                <h2 class="text-lg font-semibold">No outfits match</h2>
                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Try a different occasion.</p>
                <a href="{{ route('pick') }}" class="btn-secondary mt-6">Show any occasion</a>
            </div>
        @endif
    @endif
</x-layouts.app>
