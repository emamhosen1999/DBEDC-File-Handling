<h2 class="text-2xl font-bold text-white mb-4 holo-text">Application Settings</h2>
<p class="text-white/70 mb-6">Configure the basic settings for your application.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="6">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Application URL</label>
            <input type="url" name="app_url" class="ng-input" placeholder="http://localhost:8000" value="{{ $data['app']['url'] ?? 'http://localhost:8000' }}" required>
            <p class="text-white/50 text-xs mt-1">This URL will be used for OAuth redirects and other system functions.</p>
        </div>
    </div>

    @if(isset($errors['app']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['app'] }}
        </div>
    @endif
</form>
