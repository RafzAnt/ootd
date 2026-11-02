@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="landing-canvas min-h-screen text-stone-900 antialiased dark:text-stone-100">
    <div class="grid min-h-screen lg:grid-cols-2">
        <aside class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-rose-600 via-rose-600 to-orange-500 p-12 text-white lg:flex">
            <div class="pointer-events-none absolute -right-24 -top-24 size-96 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-16 size-96 rounded-full bg-orange-300/20 blur-3xl"></div>

            <a href="{{ route('home') }}" class="relative inline-flex items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-xl bg-white/20"><x-icon name="shirt" class="size-5" /></span>
                <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
            </a>

            <div class="relative">
                <p class="max-w-md text-4xl font-semibold leading-tight tracking-tight">
                    Stop staring at your closet. Know what to wear in seconds.
                </p>
                <p class="mt-4 max-w-md text-rose-50/90">
                    Save your best outfits once, then just pick from them whenever you need something to wear.
                </p>

                <ul class="mt-8 space-y-3 text-sm text-rose-50">
                    @foreach (['Shuffle a pick based on what you haven\'t worn lately', 'Mark favorites and tag outfits by occasion', 'Get a daily reminder at the time that suits your shift'] as $point)
                        <li class="flex items-center gap-3">
                            <span class="grid size-5 shrink-0 place-items-center rounded-full bg-white/20"><x-icon name="check" class="size-3.5" /></span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="relative text-sm text-rose-100">No daily uploads. No schedules. Just your fits.</p>
        </aside>

        <main class="flex items-center justify-center px-4 py-10 sm:px-8">
                <div class="fade-up w-full max-w-md rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_24px_80px_rgba(55,35,25,0.10)] backdrop-blur-sm sm:p-9 dark:border-stone-800 dark:bg-stone-900/85 dark:shadow-black/30">
                <a href="{{ route('home') }}" class="mb-8 inline-block lg:hidden"><x-brand /></a>
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
