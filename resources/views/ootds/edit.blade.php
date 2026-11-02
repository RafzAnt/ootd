<x-layouts.app title="Edit OOTD" subtitle="Change the details or swap the photo.">
    <form method="POST" action="{{ route('ootds.update', $ootd) }}" enctype="multipart/form-data" class="card max-w-xl space-y-6 p-6">
        @csrf
        @method('PUT')

        <div>
            <span class="label">Photo</span>
            <div class="overflow-hidden rounded-xl bg-stone-100 dark:bg-stone-800">
                <img id="photo-preview" src="{{ $ootd->imageUrl() }}" alt="{{ $ootd->altText() }}" class="max-h-96 w-full object-contain">
                <span class="hidden"></span>
            </div>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" data-photo-input="#photo-preview" class="peer sr-only">
            <label for="photo" class="btn-secondary mt-3 peer-focus-visible:ring-2 peer-focus-visible:ring-rose-500/50">Replace photo</label>
            @error('photo') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="label">Description <span class="font-normal text-stone-400">(optional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="500" class="field">{{ old('description', $ootd->description) }}</textarea>
            @error('description') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <span class="label">Good for <span class="font-normal text-stone-400">(optional)</span></span>
            <x-occasion-picker :value="old('occasion', $ootd->occasion)" />
            @error('occasion') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Save changes</button>
            <a href="{{ route('ootds.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.app>
