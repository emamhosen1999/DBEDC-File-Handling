<h2 class="text-2xl font-bold text-white mb-4 holo-text">Google OAuth Configuration</h2>
<p class="text-white/70 mb-6">Configure Google OAuth for authentication. You need to create a Google OAuth 2.0 client in the Google Cloud Console.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="8">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Google Client ID</label>
            <input type="text" name="google_client_id" class="ng-input" placeholder="Enter your Google Client ID" value="{{ $data['google_oauth']['client_id'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Google Client Secret</label>
            <input type="text" name="google_client_secret" class="ng-input" placeholder="Enter your Google Client Secret" value="{{ $data['google_oauth']['client_secret'] ?? '' }}" required>
        </div>
    </div>

    <div class="ng-alert ng-alert-warning p-4 rounded-lg mb-6">
        <strong>Important:</strong> Make sure to add http://localhost:8000/auth/google/callback as an authorized redirect URI in your Google OAuth 2.0 client settings.
    </div>

    @if(isset($errors['google_oauth']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['google_oauth'] }}
        </div>
    @endif
</form>
