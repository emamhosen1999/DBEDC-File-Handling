<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ng-btn ng-btn-primary']) }}>
    {{ $slot }}
</button>
