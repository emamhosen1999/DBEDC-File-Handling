<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ng-btn ng-btn-danger']) }}>
    {{ $slot }}
</button>
