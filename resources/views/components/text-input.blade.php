@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'ng-input']) }}>
