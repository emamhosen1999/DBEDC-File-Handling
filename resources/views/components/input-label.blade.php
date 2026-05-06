@props(['value'])

<label {{ $attributes->merge(['class' => 'ng-label']) }}>
    {{ $value ?? $slot }}
</label>
