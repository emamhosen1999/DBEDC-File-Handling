@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-[var(--color-danger)] space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
