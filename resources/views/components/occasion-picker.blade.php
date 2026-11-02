@props(['name' => 'occasion', 'value' => null])

<div class="flex flex-wrap gap-2" role="radiogroup">
    @foreach (['' => 'None'] + config('ootd.occasions') as $key => $label)
        <label class="cursor-pointer">
            <input type="radio" name="{{ $name }}" value="{{ $key }}" @checked((string) $value === (string) $key) class="sr-only">
            <span class="chip">{{ $label }}</span>
        </label>
    @endforeach
</div>
