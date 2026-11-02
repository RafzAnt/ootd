<x-layouts.app title="Upload OOTD" subtitle="Add an outfit to your lookbook. You don't have to do this every day.">
    <form method="POST" action="{{ route('ootds.store') }}" enctype="multipart/form-data" class="card max-w-xl space-y-6 p-6">
        @csrf

        <div>
            <label for="photo" class="label">Photo</label>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" required data-photo-input="#photo-preview" class="peer sr-only">
            <label for="photo"
                   class="relative flex min-h-64 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-stone-300 bg-stone-50 text-center transition hover:border-rose-400 peer-focus-visible:border-rose-500 peer-focus-visible:ring-2 peer-focus-visible:ring-rose-500/30 dark:border-stone-700 dark:bg-stone-950">
                <img id="photo-preview" alt="Preview of the selected outfit" class="hidden max-h-96 w-full object-contain">
                <span class="flex flex-col items-center gap-2 p-6 text-sm text-stone-500 dark:text-stone-400">
                    <x-icon name="upload" class="size-8" />
                    <span><span class="font-semibold text-rose-600">Choose a photo</span> or take one</span>
                    <span class="text-xs">JPG, PNG or WebP</span>
                </span>
            </label>
            @error('photo') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="label">Description <span class="font-normal text-stone-400">(optional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="500" placeholder="e.g. Black polo + jeans, good for the cold office" class="field">{{ old('description') }}</textarea>
            @error('description') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <span class="label">Good for <span class="font-normal text-stone-400">(optional)</span></span>
            <x-occasion-picker :value="old('occasion')" />
            @error('occasion') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Upload</button>
            <a href="{{ route('ootds.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.app>
