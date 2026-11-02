@props(['label', 'value', 'icon'])

<div class="card flex items-center gap-4 p-4">
    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">
        <x-icon :name="$icon" class="size-5" />
    </span>
    <div>
        <p class="text-2xl font-semibold leading-none tracking-tight">{{ $value }}</p>
        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">{{ $label }}</p>
    </div>
</div>
