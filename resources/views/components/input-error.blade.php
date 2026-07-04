@props(['for', 'bag' => null])

@if ($bag)
    @error($for, $bag)
        <p {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400']) }}>{{ $message }}</p>
    @enderror
@else
    @error($for)
        <p {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400']) }}>{{ $message }}</p>
    @enderror
@endif
