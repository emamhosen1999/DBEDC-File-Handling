@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'ng-alert ng-alert-success']) }}>
        <div class="ng-alert-icon">✓</div>
        <div class="flex-1 flex items-center text-sm font-medium">{{ $status }}</div>
    </div>
@endif
