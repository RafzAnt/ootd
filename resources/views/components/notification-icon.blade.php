@props(['kind'])

@php
    [$icon, $tone] = match ($kind) {
        'welcome' => ['smile', 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'],
        'reminder' => ['shirt', 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'],
        'milestone' => ['star', 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300'],
        'security' => ['shield', 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300'],
        default => ['bell', 'bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300'],
    };
@endphp

<span class="grid size-10 shrink-0 place-items-center rounded-xl {{ $tone }}">
    <x-icon :name="$icon" class="size-5" />
</span>
