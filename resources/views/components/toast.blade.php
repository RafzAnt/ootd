@props(['message' => ''])

<div data-toast role="status"
     class="toast-in pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-stone-200 bg-white p-3.5 pr-2.5 text-sm shadow-lg shadow-stone-900/10 dark:border-stone-700 dark:bg-stone-800">
    <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-emerald-500 text-white"><x-icon name="check" class="size-3.5" /></span>
    <p data-toast-message class="min-w-0 flex-1 break-words">{{ $message }}</p>
    <button type="button" data-toast-close aria-label="Dismiss"
            class="-my-0.5 grid size-7 shrink-0 cursor-pointer place-items-center rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-700 dark:hover:bg-stone-700 dark:hover:text-stone-200">
        <x-icon name="x" class="size-4" />
    </button>
</div>
