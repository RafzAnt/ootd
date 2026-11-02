<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="landing-canvas overflow-x-hidden text-stone-900 antialiased dark:text-stone-100">
    <header class="sticky top-0 z-30 border-b border-stone-200/70 bg-[#faf7f4]/80 backdrop-blur-xl dark:border-stone-800/80 dark:bg-stone-950/80">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3.5 sm:px-8">
            <a href="{{ route('home') }}" aria-label="{{ config('app.name') }} home"><x-brand /></a>
            <nav class="flex items-center gap-2 sm:gap-5" aria-label="Landing page">
                <a href="#features" class="hidden text-sm font-medium text-stone-600 transition hover:text-rose-600 sm:inline dark:text-stone-300">Features</a>
                <a href="#how-it-works" class="hidden text-sm font-medium text-stone-600 transition hover:text-rose-600 sm:inline dark:text-stone-300">How it works</a>
                <a href="{{ route('login') }}" class="btn-secondary px-3.5 py-2 sm:px-4">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary px-3.5 py-2 sm:px-4">Sign up</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="relative isolate mx-auto grid min-h-[calc(100svh_-_4.5rem)] max-w-6xl items-center gap-12 px-4 py-16 sm:px-8 sm:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:gap-8 lg:py-24">
            <div class="fade-up relative z-10">
                <span class="inline-flex items-center gap-2 rounded-full border border-rose-200/80 bg-white/70 px-3.5 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-rose-700 shadow-sm dark:border-rose-500/20 dark:bg-stone-900/70 dark:text-rose-300">
                    <span class="size-1.5 rounded-full bg-rose-500"></span> Your digital wardrobe
                </span>
                <h1 class="mt-6 max-w-2xl text-5xl font-semibold leading-[1.04] tracking-[-0.065em] sm:text-6xl lg:text-7xl">
                    Get dressed with <span class="bg-gradient-to-r from-rose-600 via-rose-500 to-orange-500 bg-clip-text text-transparent">less guesswork.</span>
                </h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-stone-600 sm:text-lg sm:leading-8 dark:text-stone-300">
                    Keep the outfits you love in one place. Pick a look in seconds, plan ahead, and remember what you wore.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-3.5">Build my lookbook <x-icon name="arrow-right" class="size-4" /></a>
                    <a href="#how-it-works" class="btn-secondary px-6 py-3.5">See how it works</a>
                </div>
                <div class="mt-9 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-stone-500 dark:text-stone-400">
                    <span class="inline-flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> Made for your real wardrobe</span>
                    <span class="inline-flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> Easy to update any time</span>
                </div>
            </div>

            <div class="fade-up relative mx-auto flex min-h-[26rem] w-full max-w-lg items-center justify-center sm:min-h-[32rem]" aria-label="Illustration of a digital outfit lookbook">
                <div class="absolute inset-x-8 inset-y-10 rotate-[-9deg] rounded-[2.5rem] bg-gradient-to-br from-stone-900 to-stone-700 shadow-2xl shadow-stone-900/20 dark:from-stone-800 dark:to-stone-900"></div>
                <div class="absolute inset-x-5 inset-y-7 rotate-[7deg] rounded-[2.5rem] bg-gradient-to-br from-amber-300 via-orange-400 to-orange-500 shadow-2xl shadow-orange-900/20"></div>
                <div class="relative z-10 w-[min(76%,19rem)] -rotate-2 rounded-[2.2rem] border border-white/60 bg-gradient-to-br from-rose-500 via-rose-600 to-orange-500 p-5 text-white shadow-[0_35px_90px_rgba(190,24,93,0.28)] sm:p-6">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-white/80">Today's pick</span>
                        <span class="grid size-9 place-items-center rounded-full bg-white/15"><x-icon name="sparkles" class="size-4" /></span>
                    </div>
                    <div class="my-7 grid aspect-[4/4.4] place-items-center overflow-hidden rounded-[1.65rem] border border-white/20 bg-gradient-to-br from-rose-300/30 via-orange-200/20 to-white/10">
                        <span class="grid size-32 place-items-center rounded-[2rem] bg-white/15 shadow-inner ring-1 ring-white/25 backdrop-blur-sm sm:size-36">
                            <x-icon name="shirt" class="size-20 stroke-[1.25] sm:size-24" />
                        </span>
                    </div>
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-lg font-semibold tracking-tight">Your favorite fit</p>
                            <p class="mt-1 text-sm text-white/75">Ready when you are</p>
                        </div>
                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-rose-600 shadow-lg"><x-icon name="heart" class="size-5" /></span>
                    </div>
                </div>
                <div class="absolute -left-1 top-10 z-20 flex items-center gap-2 rounded-2xl border border-white/80 bg-white/90 px-4 py-3 text-sm font-semibold shadow-xl shadow-stone-900/10 backdrop-blur dark:border-stone-700 dark:bg-stone-900/90">
                    <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300"><x-icon name="check" class="size-5" /></span>
                    Outfit planned
                </div>
                <div class="absolute -right-1 bottom-10 z-20 flex items-center gap-2 rounded-2xl border border-white/80 bg-white/90 px-4 py-3 text-sm font-semibold shadow-xl shadow-stone-900/10 backdrop-blur dark:border-stone-700 dark:bg-stone-900/90">
                    <span class="grid size-9 place-items-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300"><x-icon name="dice" class="size-5" /></span>
                    One-tap outfit picks
                </div>
                <span class="pointer-events-none absolute left-1/2 top-1/2 -z-10 size-[23rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-rose-400/15 blur-3xl"></span>
            </div>
        </section>

        <section id="how-it-works" class="scroll-mt-24 border-y border-stone-200/70 bg-white/55 py-20 sm:py-24 dark:border-stone-800 dark:bg-stone-900/35">
            <div class="mx-auto max-w-6xl px-4 sm:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-600 dark:text-rose-300">A little less morning stress</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.05em] sm:text-5xl">Your style, in three simple steps.</h2>
                    <p class="mt-4 text-stone-600 dark:text-stone-400">Set up your lookbook once, then let it make getting dressed easier every day.</p>
                </div>

                <ol class="mt-12 grid gap-4 md:grid-cols-3">
                    @foreach ([
                        ['01', 'Save the looks you love', 'Add outfit photos, a short note, and an occasion if you want.', 'upload'],
                        ['02', 'Let the app choose', 'Get a fresh suggestion that gives your less-worn outfits a turn.', 'dice'],
                        ['03', 'Wear it or plan ahead', 'Log what you wore today or schedule looks for the week ahead.', 'calendar'],
                    ] as [$number, $title, $text, $icon])
                        <li class="group relative overflow-hidden rounded-[1.7rem] border border-stone-200/80 bg-white/80 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-900/[0.06] sm:p-8 dark:border-stone-800 dark:bg-stone-900/80">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold tracking-[0.14em] text-rose-500/70">{{ $number }}</span>
                                <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-rose-50 to-orange-50 text-rose-600 transition group-hover:scale-105 dark:from-rose-500/10 dark:to-orange-500/10 dark:text-rose-300"><x-icon :name="$icon" class="size-5" /></span>
                            </div>
                            <h3 class="mt-8 text-lg font-semibold tracking-tight">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-stone-400">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section id="features" class="scroll-mt-24 mx-auto max-w-6xl px-4 py-20 sm:px-8 sm:py-28">
            <div class="grid items-end gap-6 md:grid-cols-[1fr_auto]">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-600 dark:text-rose-300">More than a photo gallery</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.05em] sm:text-5xl">A home for all the outfits you already own.</h2>
                </div>
                <p class="max-w-sm text-sm leading-6 text-stone-500 dark:text-stone-400">Keep your favorites close, remember what you wore, and make your wardrobe work harder.</p>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['dice', 'A thoughtful outfit picker', 'Shuffle a look for today, with a nudge toward outfits you have not worn in a while.', 'from-rose-500 to-orange-500'],
                    ['calendar', 'A week with a plan', 'Choose tomorrow’s outfit ahead of time and see your upcoming looks together.', 'from-violet-500 to-indigo-500'],
                    ['heart', 'Your personal favorites', 'Save the looks you love and find them quickly when you need a reliable outfit.', 'from-pink-500 to-rose-500'],
                    ['clock', 'A visual wear history', 'Look back at what you wore and avoid repeating the same outfit by accident.', 'from-amber-500 to-orange-500'],
                    ['grid', 'A wardrobe that stays organized', 'Search your collection, filter by occasion, and sort by what you wear most.', 'from-emerald-500 to-teal-500'],
                    ['bell', 'Reminders on your schedule', 'Choose a daily nudge time that works for your routine and your shift.', 'from-sky-500 to-blue-500'],
                ] as [$icon, $title, $text, $gradient])
                    <article class="group rounded-[1.6rem] border border-stone-200/80 bg-white/75 p-6 transition duration-300 hover:-translate-y-1 hover:border-rose-200 hover:bg-white hover:shadow-xl hover:shadow-stone-900/[0.06] sm:p-7 dark:border-stone-800 dark:bg-stone-900/70 dark:hover:border-rose-500/30 dark:hover:bg-stone-900">
                        <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br {{ $gradient }} text-white shadow-lg shadow-stone-900/10 transition duration-300 group-hover:rotate-3 group-hover:scale-105">
                            <x-icon :name="$icon" class="size-5" />
                        </span>
                        <h3 class="mt-5 font-semibold tracking-tight">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-stone-400">{{ $text }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="planner" class="scroll-mt-24 overflow-hidden border-y border-stone-200/70 bg-gradient-to-br from-rose-50/90 via-orange-50/70 to-white py-20 sm:py-24 dark:border-stone-800 dark:from-rose-950/30 dark:via-stone-900 dark:to-stone-950">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 sm:px-8 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <span class="grid size-12 place-items-center rounded-2xl bg-white text-rose-600 shadow-md shadow-rose-900/5 dark:bg-stone-800 dark:text-rose-300"><x-icon name="calendar" class="size-5" /></span>
                    <p class="mt-6 text-xs font-semibold uppercase tracking-[0.18em] text-rose-600 dark:text-rose-300">Meet your future self, already dressed</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight tracking-[-0.05em] sm:text-5xl">Make tomorrow morning feel easy.</h2>
                    <p class="mt-4 max-w-lg leading-7 text-stone-600 dark:text-stone-400">Pick an outfit for a day coming up. Your seven-day view keeps the plan simple, and you can change it whenever plans change.</p>
                    <a href="{{ route('register') }}" class="btn-secondary mt-7 inline-flex px-5 py-3">Start planning <x-icon name="arrow-right" class="size-4" /></a>
                </div>

                <div class="relative mx-auto w-full max-w-xl">
                    <div class="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-rose-200/70 to-orange-200/60 blur-2xl dark:from-rose-900/20 dark:to-orange-900/10"></div>
                    <div class="relative rounded-[1.8rem] border border-white/90 bg-white/90 p-5 shadow-[0_25px_80px_rgba(92,43,43,0.12)] sm:p-7 dark:border-stone-700 dark:bg-stone-900/90 dark:shadow-black/30">
                        <div class="flex items-center justify-between border-b border-stone-100 pb-4 dark:border-stone-800">
                            <div>
                                <p class="text-sm font-semibold">The week ahead</p>
                                <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">A little plan, a lot less thinking</p>
                            </div>
                            <span class="grid size-10 place-items-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300"><x-icon name="sparkles" class="size-5" /></span>
                        </div>
                        <div class="mt-4 space-y-2.5">
                            @foreach ([
                                ['MON', 'Today', 'Casual layers', 'from-rose-400 to-orange-300'],
                                ['TUE', 'Tomorrow', 'Ready for the day', 'from-violet-400 to-indigo-300'],
                                ['WED', 'Wednesday', 'A favorite look', 'from-emerald-400 to-teal-300'],
                            ] as [$day, $label, $outfit, $gradient])
                                <div class="flex items-center gap-3 rounded-2xl border border-stone-100 p-3 dark:border-stone-800">
                                    <div class="w-12 shrink-0 text-center">
                                        <p class="text-[10px] font-semibold tracking-wider text-stone-400">{{ $day }}</p>
                                        <p class="mt-0.5 text-xs font-medium text-stone-600 dark:text-stone-300">{{ $label }}</p>
                                    </div>
                                    <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $gradient }} text-white shadow-sm"><x-icon name="shirt" class="size-6" /></div>
                                    <p class="min-w-0 flex-1 truncate text-sm font-medium">{{ $outfit }}</p>
                                    <span class="grid size-8 place-items-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300"><x-icon name="check" class="size-4" /></span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 flex items-center gap-2 rounded-xl bg-stone-50 px-3.5 py-3 text-xs text-stone-500 dark:bg-stone-800/70 dark:text-stone-400">
                            <x-icon name="heart" class="size-4 text-rose-500" /> Your outfits, your plans, all in one place.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-20 sm:px-8 sm:py-28">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-rose-600 via-rose-600 to-orange-500 px-6 py-12 text-center text-white shadow-2xl shadow-rose-900/20 sm:px-12 sm:py-16">
                <span class="pointer-events-none absolute -right-12 -top-24 size-72 rounded-full bg-white/10 blur-2xl"></span>
                <span class="pointer-events-none absolute -bottom-28 -left-10 size-80 rounded-full bg-orange-300/20 blur-3xl"></span>
                <div class="relative mx-auto max-w-2xl">
                    <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20"><x-icon name="shirt" class="size-6" /></span>
                    <h2 class="mt-5 text-3xl font-semibold tracking-[-0.05em] sm:text-5xl">Your next favorite outfit is already yours.</h2>
                    <p class="mx-auto mt-4 max-w-xl leading-7 text-rose-50/90">Make a lookbook from what you own, then make choosing what to wear the easiest part of your day.</p>
                    <a href="{{ route('register') }}" class="mt-7 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-rose-700 shadow-lg transition hover:-translate-y-0.5 hover:bg-rose-50">Create your account <x-icon name="arrow-right" class="size-4" /></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-stone-200/70 bg-white/45 dark:border-stone-800 dark:bg-stone-900/30">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-7 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <a href="{{ route('home') }}"><x-brand /></a>
            <p class="text-sm text-stone-500 dark:text-stone-400">A calmer way to choose what to wear.</p>
            <div class="flex items-center gap-4 text-sm font-medium text-stone-600 dark:text-stone-300">
                <a href="{{ route('login') }}" class="transition hover:text-rose-600">Log in</a>
                <a href="{{ route('register') }}" class="transition hover:text-rose-600">Get started</a>
            </div>
        </div>
    </footer>
</body>
</html>
