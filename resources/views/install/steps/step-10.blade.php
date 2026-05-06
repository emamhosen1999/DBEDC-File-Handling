<h2 class="text-2xl font-bold text-white mb-4 holo-text">Email Configuration</h2>
<p class="text-white/70 mb-6">Configure email server settings for notifications. This is mandatory.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="10">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">SMTP Host</label>
            <input type="text" name="mail_host" class="ng-input" placeholder="smtp.gmail.com" value="{{ $data['email']['host'] ?? 'smtp.gmail.com' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">SMTP Port</label>
            <input type="number" name="mail_port" class="ng-input" placeholder="587" value="{{ $data['email']['port'] ?? '587' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Encryption</label>
            <select name="mail_secure" class="ng-input">
                <option value="tls" {{ ($data['email']['secure'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ ($data['email']['secure'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                <option value="none" {{ ($data['email']['secure'] ?? 'tls') === 'none' ? 'selected' : '' }}>None</option>
            </select>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">From Email</label>
            <input type="email" name="mail_from_email" class="ng-input" placeholder="noreply@example.com" value="{{ $data['email']['from_email'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">From Name</label>
            <input type="text" name="mail_from_name" class="ng-input" placeholder="DBEDC File Tracker" value="{{ $data['email']['from_name'] ?? 'DBEDC File Tracker' }}">
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">SMTP Username</label>
            <input type="text" name="mail_username" class="ng-input" placeholder="your-email@gmail.com" value="{{ $data['email']['username'] ?? '' }}">
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">SMTP Password</label>
            <input type="password" name="mail_password" class="ng-input" placeholder="Enter SMTP password" value="{{ $data['email']['password'] ?? '' }}">
        </div>
    </div>

    @if(isset($errors['email']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['email'] }}
        </div>
    @endif
</form>
