@props(['title', 'subtitle' => null, 'actions' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="app-canvas text-stone-900 antialiased dark:text-stone-100">
    <div class="min-h-screen lg:flex">
        <div id="sidebar-backdrop" data-sidebar-toggle class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>

        <aside id="sidebar"
               class="app-sidebar fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-stone-200 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 dark:border-stone-800">
            <a href="{{ route('dashboard') }}" class="border-b border-stone-100 px-5 py-5 dark:border-stone-800/80"><x-brand /></a>

            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-2" aria-label="Main">
                @foreach (config('dashboard.nav') as $section)
                    <div class="space-y-1">
                        @if ($section['heading'])
                            <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-stone-400 dark:text-stone-500">{{ $section['heading'] }}</p>
                        @endif

                        @foreach ($section['items'] as $item)
                            @php($active = request()->routeIs(...$item['active']))
                            <a href="{{ route($item['route']) }}"
                               @class([
                                   'nav-link',
                                   'nav-link-active' => $active,
                                   'text-stone-600 hover:bg-stone-100/80 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800/80 dark:hover:text-stone-100' => ! $active,
                               ])
                               @if ($active) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" class="size-5 shrink-0" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-stone-200 p-3 dark:border-stone-800">
                <div class="mb-2 flex items-center gap-3 rounded-2xl bg-stone-50/80 px-2.5 py-3 dark:bg-stone-800/50">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-rose-500 via-rose-600 to-orange-500 text-sm font-semibold uppercase text-white shadow-md shadow-rose-900/20">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-stone-500 dark:text-stone-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mt-1 flex w-full cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-stone-100">
                        <x-icon name="logout" class="size-5 shrink-0" />
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="app-topbar sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-stone-200/80 px-4 sm:px-8 dark:border-stone-800/80">
                <button type="button" data-sidebar-toggle aria-label="Open menu"
                        class="-ml-1 grid size-10 cursor-pointer place-items-center rounded-lg text-stone-600 hover:bg-stone-100 lg:hidden dark:text-stone-300 dark:hover:bg-stone-800">
                    <x-icon name="menu" class="size-6" />
                </button>
                <span class="lg:hidden"><x-brand /></span>

                <div class="ml-auto flex items-center gap-1">
                    <x-notification-bell />
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-10 lg:px-10">
                <div class="mb-8 flex flex-wrap items-end justify-between gap-4 sm:mb-10">
                    <div>
                        <h1 class="page-title">{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="page-subtitle">{{ $subtitle }}</p>
                        @endif
                    </div>
                    {{ $actions }}
                </div>

                {{ $slot }}
            </main>
        </div>
    </div>

    <div id="toasts" aria-live="polite"
         class="pointer-events-none fixed inset-x-4 bottom-4 z-50 flex flex-col items-center gap-2 sm:inset-x-auto sm:bottom-6 sm:right-6 sm:items-end">
        @if (session('status'))
            <x-toast :message="session('status')" />
        @endif
    </div>
    <template id="toast-template"><x-toast /></template>

    <dialog id="lightbox" class="m-auto max-h-[92vh] w-[min(92vw,40rem)] overflow-visible bg-transparent p-0 text-white">
        <button type="button" data-lightbox-close aria-label="Close"
                class="absolute -top-3 -right-3 z-10 grid size-9 cursor-pointer place-items-center rounded-full bg-white text-stone-900 shadow-lg">
            <x-icon name="x" class="size-5" />
        </button>
        <img data-lightbox-image alt="" class="max-h-[82vh] w-full rounded-2xl bg-stone-900 object-contain">
        <p data-lightbox-caption hidden class="mt-3 text-center text-sm text-stone-200"></p>
    </dialog>
</body>
</html>
