<x-layouts.app title="Outfit planner" subtitle="Plan your looks for the next 30 days and make mornings easier.">
    @if ($outfits->isEmpty())
        <section class="card empty-state flex flex-col items-center px-6 py-16 text-center">
            <span class="grid size-14 place-items-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">
                <x-icon name="calendar" class="size-7" />
            </span>
            <h2 class="mt-5 text-lg font-semibold">Add outfits before you plan</h2>
            <p class="mt-1 max-w-sm text-sm text-stone-500 dark:text-stone-400">No OOTD saved yet. Open <strong>Upload OOTD</strong> from the menu to add an outfit before planning.</p>
        </section>
    @else
        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.8fr)]">
            <section class="card p-5 sm:p-6" aria-labelledby="week-title">
                <div class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">
                        <x-icon name="calendar" class="size-5" />
                    </span>
                    <div>
                        <h2 id="week-title" class="font-semibold">The week ahead</h2>
                        <p class="text-sm text-stone-500 dark:text-stone-400">Your planned looks at a glance</p>
                    </div>
                </div>

                <ol class="mt-5 space-y-3">
                    @foreach ($days as $day)
                        <li class="flex items-center gap-4 rounded-xl border border-stone-200 p-3 dark:border-stone-800">
                            <div class="w-16 shrink-0 text-center">
                                <p class="text-xs font-medium uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $day['date']->isToday() ? 'Today' : $day['date']->format('D') }}</p>
                                <p class="mt-0.5 text-xl font-semibold tabular-nums">{{ $day['date']->format('j') }}</p>
                            </div>

                            @if ($day['plan'])
                                <a href="{{ route('ootds.edit', $day['plan']->ootd) }}" class="flex min-w-0 flex-1 items-center gap-3">
                                    <img src="{{ $day['plan']->ootd->imageUrl() }}" alt="{{ $day['plan']->ootd->altText() }}" class="size-12 shrink-0 rounded-lg object-cover">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold">{{ $day['plan']->ootd->description ?: 'Outfit planned' }}</span>
                                        <span class="mt-0.5 block text-xs text-stone-500 dark:text-stone-400">{{ $day['plan']->ootd->occasionLabel() ?: 'No occasion set' }}</span>
                                    </span>
                                </a>
                                <form method="POST" action="{{ route('planner.destroy', $day['plan']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="grid size-9 shrink-0 place-items-center rounded-lg text-stone-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10" aria-label="Remove plan for {{ $day['date']->format('F j') }}">
                                        <x-icon name="x" class="size-4" />
                                    </button>
                                </form>
                            @else
                                <p class="min-w-0 flex-1 text-sm text-stone-400 dark:text-stone-500">Nothing planned yet</p>
                                <button type="button" data-plan-date="{{ $day['date']->toDateString() }}" class="rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10">Plan</button>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>

            <section id="plan-form" class="card p-5 sm:p-6" aria-labelledby="plan-title">
                <h2 id="plan-title" class="font-semibold">Plan an outfit</h2>
                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Choose a look and a date. A new choice replaces that day's plan.</p>

                <form method="POST" action="{{ route('planner.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="ootd_id" class="label">Outfit</label>
                        <select id="ootd_id" name="ootd_id" required class="field">
                            <option value="">Choose an outfit</option>
                            @foreach ($outfits as $outfit)
                                <option value="{{ $outfit->id }}" @selected(old('ootd_id') == $outfit->id)>{{ $outfit->description ?: 'Outfit uploaded '.$outfit->created_at->format('M j, Y') }}{{ $outfit->occasionLabel() ? ' · '.$outfit->occasionLabel() : '' }}</option>
                            @endforeach
                        </select>
                        @error('ootd_id') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="planned_for" class="label">Date</label>
                        <input id="planned_for" name="planned_for" type="date" min="{{ today()->toDateString() }}" max="{{ $maxDate }}" value="{{ old('planned_for', today()->toDateString()) }}" required class="field">
                        @error('planned_for') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full"><x-icon name="plus" class="size-4" /> Save plan</button>
                </form>

                <p class="mt-4 rounded-xl bg-stone-50 px-4 py-3 text-xs leading-relaxed text-stone-500 dark:bg-stone-800/60 dark:text-stone-400">You can plan up to 30 days ahead. Setting another outfit for the same date updates your plan.</p>
            </section>
        </div>

        @if ($plans->isNotEmpty())
            <section class="mt-10" aria-labelledby="all-plans-title">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h2 id="all-plans-title" class="text-lg font-semibold">Upcoming plans</h2>
                        <p class="text-sm text-stone-500 dark:text-stone-400">{{ $plans->count() }} {{ Str::plural('outfit', $plans->count()) }} scheduled in the next 30 days</p>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($plans as $plan)
                        <article class="card flex items-center gap-3 p-3">
                            <a href="{{ $plan->ootd->imageUrl() }}" data-lightbox data-caption="{{ $plan->ootd->altText() }}" class="shrink-0">
                                <img src="{{ $plan->ootd->imageUrl() }}" alt="{{ $plan->ootd->altText() }}" loading="lazy" class="size-16 rounded-xl object-cover">
                            </a>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-rose-600 dark:text-rose-300">{{ $plan->planned_for->format('D, M j') }}</p>
                                <p class="mt-0.5 truncate text-sm font-semibold">{{ $plan->ootd->description ?: 'Outfit planned' }}</p>
                                <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">{{ $plan->ootd->occasionLabel() ?: 'No occasion set' }}</p>
                            </div>
                            <form method="POST" action="{{ route('planner.destroy', $plan) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="grid size-9 place-items-center rounded-lg text-stone-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10" aria-label="Remove plan for {{ $plan->planned_for->format('F j') }}">
                                    <x-icon name="trash" class="size-4" />
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    @endif

</x-layouts.app>
