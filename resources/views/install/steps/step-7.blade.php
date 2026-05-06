<h2 class="text-2xl font-bold text-white mb-4 holo-text">Branding Configuration</h2>
<p class="text-white/70 mb-6">Configure your company branding. This is mandatory for proper application display.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="7">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Company Name</label>
            <input type="text" name="company_name" class="ng-input" placeholder="Your Company Name" value="{{ $data['branding']['company_name'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Company Logo URL</label>
            <input type="url" name="company_logo" class="ng-input" placeholder="https://example.com/logo.png" value="{{ $data['branding']['company_logo'] ?? '' }}">
            <p class="text-white/50 text-xs mt-1">Optional: URL to your company logo</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/80 mb-2 text-sm font-medium">Primary Color</label>
                <input type="color" name="primary_color" class="w-full h-10 rounded-lg cursor-pointer" value="{{ $data['branding']['primary_color'] ?? '#667eea' }}">
            </div>
            <div>
                <label class="block text-white/80 mb-2 text-sm font-medium">Secondary Color</label>
                <input type="color" name="secondary_color" class="w-full h-10 rounded-lg cursor-pointer" value="{{ $data['branding']['secondary_color'] ?? '#764ba2' }}">
            </div>
        </div>
    </div>

    @if(isset($errors['branding']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['branding'] }}
        </div>
    @endif
</form>
