<x-layouts.app :title="$greeting.', '.strtok(auth()->user()->name, ' ')" :subtitle="$today ? 'You\'re all set for today.' : 'Ready to pick something to wear?'">
    @if ($total === 0)
        <section class="card fade-up overflow-hidden">
            <div class="bg-gradient-to-br from-rose-50 to-orange-50 px-6 py-10 text-center sm:px-10 dark:from-rose-500/10 dark:to-orange-500/5">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-rose-500 to-orange-500 text-white shadow-lg shadow-rose-600/25">
                    <x-icon name="shirt" class="size-7" />
                </span>
                <h2 class="mt-5 text-2xl font-semibold tracking-tight">Let's build your lookbook</h2>
                <p class="mx-auto mt-2 max-w-md text-stone-600 dark:text-stone-400">
                    No OOTD saved yet. Open <strong>Upload OOTD</strong> from the menu to add your first outfit.
                </p>
            </div>

            <ol class="grid divide-y divide-stone-100 sm:grid-cols-3 sm:divide-x sm:divide-y-0 dark:divide-stone-800">
                @foreach ([
                    ['Upload', 'Add a photo of an outfit. A description is optional.'],
                    ['Pick', 'Hit "Pick for me" and get a suggestion in seconds.'],
                    ['Wear', 'Tap "Wear today" so you remember what you wore.'],
                ] as $i => [$step, $text])
                    <li class="flex items-start gap-4 p-6">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-rose-50 text-sm font-semibold text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">{{ $i + 1 }}</span>
                        <div>
                            <p class="font-semibold">{{ $step }}</p>
                            <p class="mt-0.5 text-sm text-stone-500 dark:text-stone-400">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Today --}}
            <div class="lg:col-span-2">
                @if ($today)
                    <section class="fade-up h-full overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white shadow-sm dark:border-emerald-500/20 dark:from-emerald-500/10 dark:to-stone-900">
                        <div class="grid h-full sm:grid-cols-[14rem_1fr]">
                            <a href="{{ $today->imageUrl() }}" data-lightbox data-caption="{{ $today->altText() }}" class="block bg-stone-100 dark:bg-stone-800">
                                <img src="{{ $today->imageUrl() }}" alt="{{ $today->altText() }}" class="aspect-[4/3] size-full object-cover object-top sm:aspect-auto">
                            </a>
                            <div class="flex flex-col justify-center p-6 sm:p-8">
                                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300">
                                    <x-icon name="check" class="size-3.5" /> Wearing today
                                </span>
                                <h2 class="mt-4 text-2xl font-semibold tracking-tight">{{ $today->description ?: 'Your outfit for today' }}</h2>
                                @if ($today->occasionLabel())
                                    <span class="pill mt-3 w-fit">{{ $today->occasionLabel() }}</span>
                                @endif
                                <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">Looking good. Changed your mind?</p>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="{{ route('pick') }}" class="btn-secondary"><x-icon name="dice" class="size-4" /> Pick something else</a>
                                    <form method="POST" action="{{ route('ootds.unwear', $today) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary">Undo</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                @elseif ($suggestion)
                    <section class="fade-up relative h-full overflow-hidden rounded-3xl bg-gradient-to-br from-rose-600 via-rose-500 to-orange-500 text-white shadow-lg shadow-rose-600/20">
                        <div class="pointer-events-none absolute -right-20 -top-24 size-80 rounded-full bg-white/10 blur-2xl"></div>
                        <div class="pointer-events-none absolute -bottom-24 left-1/4 size-72 rounded-full bg-orange-300/20 blur-3xl"></div>

                        <div class="relative grid h-full gap-8 p-6 sm:p-8 md:grid-cols-[1fr_14rem] md:items-center">
                            <div class="order-2 md:order-1">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                                    <x-icon name="dice" class="size-3.5" /> Suggested for you
                                </span>

                                <h2 @class(['mt-4 text-3xl font-semibold leading-tight tracking-tight', 'text-white/80' => ! $suggestion->description])>
                                    {{ $suggestion->description ?: 'Today\'s pick' }}
                                </h2>

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-rose-50">
                                    @if ($suggestion->occasionLabel())
                                        <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-medium">{{ $suggestion->occasionLabel() }}</span>
                                    @endif
                                    <span>{{ $suggestion->wearSummary() }}</span>
                                </div>

                                <div class="mt-7 flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('ootds.wear', $suggestion) }}">
                                        @csrf
                                        <input type="hidden" name="then" value="dashboard">
                                        <button type="submit" class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-semibold text-rose-700 shadow-sm transition hover:bg-rose-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-rose-600">
                                            <x-icon name="shirt" class="size-4" /> Wear this today
                                        </button>
                                    </form>
                                    <a href="{{ route('dashboard', ['shuffle' => 1]) }}" class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-rose-600">
                                        <x-icon name="refresh" class="size-4" /> Shuffle
                                    </a>
                                </div>

                                <p class="mt-5 text-xs text-rose-100/90">We lean towards outfits you haven't worn in a while.</p>
                            </div>

                            <div class="relative order-1 mx-auto w-44 md:order-2 md:w-full">
                                <a href="{{ $suggestion->imageUrl() }}" data-lightbox data-caption="{{ $suggestion->altText() }}" class="block">
                                    <img src="{{ $suggestion->imageUrl() }}" alt="{{ $suggestion->altText() }}"
                                         class="aspect-[3/4] w-full rounded-2xl border-4 border-white/90 object-cover shadow-2xl shadow-rose-900/30 md:rotate-2">
                                </a>
                                <x-favorite-button :ootd="$suggestion" class="absolute right-3 top-3" />
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            <div class="space-y-6">
                {{-- This month --}}
                <section class="card fade-up p-5" aria-labelledby="month-title">
                    <div class="flex items-baseline justify-between">
                        <h2 id="month-title" class="text-sm font-semibold">{{ $month['name'] }}</h2>
                        <a href="{{ route('history') }}" class="text-xs font-semibold text-rose-600 hover:underline">History</a>
                    </div>

                    <p class="mt-3 text-4xl font-semibold tracking-tight">{{ count($month['worn']) }}</p>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ Str::plural('outfit', count($month['worn'])) }} logged this month</p>

                    <div class="mt-4 grid justify-items-center gap-y-2" role="img"
                         style="grid-template-columns: repeat({{ (int) ceil($month['days'] / 2) }}, minmax(0, 1fr))"
                         aria-label="{{ count($month['worn']) }} of the first {{ $month['day'] }} days of {{ $month['name'] }} have an outfit logged">
                        @for ($d = 1; $d <= $month['days']; $d++)
                            @php($worn = in_array($d, $month['worn'], true))
                            <span title="{{ $month['name'] }} {{ $d }}{{ $worn ? ' · outfit logged' : '' }}"
                                  @class([
                                      'size-2.5 rounded-full',
                                      'bg-rose-500' => $worn,
                                      'bg-stone-200 dark:bg-stone-700' => ! $worn && $d <= $month['day'],
                                      'bg-stone-50 ring-1 ring-inset ring-stone-200 dark:bg-transparent dark:ring-stone-700' => ! $worn && $d > $month['day'],
                                      'ring-2 ring-rose-300 ring-offset-1 ring-offset-white dark:ring-rose-500/60 dark:ring-offset-stone-900' => $d === $month['day'],
                                  ])></span>
                        @endfor
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-xs text-stone-500 dark:text-stone-400">
                        <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-rose-500"></span> Outfit logged</span>
                        <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-stone-200 dark:bg-stone-700"></span> Nothing logged</span>
                    </div>
                </section>

                {{-- Wardrobe --}}
                <section class="card fade-up p-5" aria-labelledby="wardrobe-title">
                    <div class="flex items-baseline justify-between">
                        <h2 id="wardrobe-title" class="text-sm font-semibold">Your wardrobe</h2>
                        <a href="{{ route('ootds.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">Manage</a>
                    </div>

                    <p class="mt-3 text-4xl font-semibold tracking-tight">{{ $total }}</p>
                    <p class="text-sm text-stone-500 dark:text-stone-400">
                        saved {{ Str::plural('outfit', $total) }}
                        <span aria-hidden="true">·</span>
                        <a href="{{ route('ootds.index', ['favorites' => 1]) }}" class="hover:text-rose-600">{{ $favorites }} {{ Str::plural('favorite', $favorites) }}</a>
                        @if ($neverWorn > 0)
                            <span aria-hidden="true">·</span>
                            <a href="{{ route('ootds.index', ['sort' => 'least_recent']) }}" class="hover:text-rose-600">{{ $neverWorn }} never worn</a>
                        @endif
                    </p>

                    @if (collect($mix)->contains(fn ($row) => $row['slot'] !== 'other'))
                        <div class="mt-5 flex items-center gap-0.5" role="group"
                             aria-label="Outfits by occasion: {{ collect($mix)->map(fn ($row) => $row['label'].' '.$row['count'])->implode(', ') }}">
                            @foreach ($mix as $i => $row)
                                <div class="group relative flex h-8 min-w-1.5 items-center" style="flex: {{ $row['count'] }} 1 0%"
                                     tabindex="0" role="img" aria-label="{{ $row['label'] }}: {{ $row['count'] }} {{ Str::plural('outfit', $row['count']) }} ({{ $row['percent'] }}%)">
                                    <span class="block h-5 w-full {{ $loop->last ? 'rounded-r' : '' }}" style="background-color: var(--series-{{ $row['slot'] }})"></span>
                                    <span role="tooltip"
                                          @class([
                                              'pointer-events-none absolute bottom-full z-10 mb-1 whitespace-nowrap rounded-md bg-stone-900 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 shadow-lg transition group-hover:opacity-100 group-focus-visible:opacity-100 dark:bg-stone-100 dark:text-stone-900',
                                              'left-0' => $loop->first && ! $loop->last,
                                              'right-0' => $loop->last && ! $loop->first,
                                              'left-1/2 -translate-x-1/2' => ! $loop->first && ! $loop->last,
                                          ])>
                                        {{ $row['label'] }} · {{ $row['count'] }} ({{ $row['percent'] }}%)
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <ul class="mt-3 space-y-1.5">
                            @foreach ($mix as $row)
                                <li class="flex items-center gap-2.5 text-sm">
                                    <span class="size-2.5 shrink-0 rounded-full" style="background-color: var(--series-{{ $row['slot'] }})"></span>
                                    <span class="min-w-0 flex-1 truncate">{{ $row['label'] }}</span>
                                    <span class="font-medium tabular-nums">{{ $row['count'] }}</span>
                                    <span class="w-10 text-right text-xs tabular-nums text-stone-500 dark:text-stone-400">{{ $row['percent'] }}%</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 rounded-xl bg-stone-50 px-4 py-3 text-sm text-stone-500 dark:bg-stone-800/50 dark:text-stone-400">
                            Tag your outfits by occasion to see how your wardrobe splits.
                            <a href="{{ route('ootds.index') }}" class="font-semibold text-rose-600 hover:underline">Open Manage</a>
                        </p>
                    @endif
                </section>
            </div>
        </div>

        @if ($dustOff->count() >= 2)
            <section class="mt-10">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Not worn in a while</h2>
                        <p class="text-sm text-stone-500 dark:text-stone-400">Give these a turn.</p>
                    </div>
                    <a href="{{ route('ootds.index', ['sort' => 'least_recent']) }}" class="text-sm font-semibold text-rose-600 hover:underline">See all</a>
                </div>
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    @foreach ($dustOff as $ootd)
                        <x-outfit-tile :ootd="$ootd" :today-id="$today?->id" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-10">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Recently added</h2>
                <a href="{{ route('ootds.index') }}" class="text-sm font-semibold text-rose-600 hover:underline">See all</a>
            </div>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                @foreach ($recent as $ootd)
                    <a href="{{ $ootd->imageUrl() }}" data-lightbox data-caption="{{ $ootd->altText() }}"
                       class="group block overflow-hidden rounded-xl bg-stone-100 ring-1 ring-black/5 dark:bg-stone-800 dark:ring-white/10">
                        <img src="{{ $ootd->imageUrl() }}" alt="{{ $ootd->altText() }}" loading="lazy"
                             class="aspect-[3/4] w-full object-cover transition duration-300 group-hover:scale-105">
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
