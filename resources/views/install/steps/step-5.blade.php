<h2 class="text-2xl font-bold text-white mb-4 holo-text">Admin Account Setup</h2>
<p class="text-white/70 mb-6">Create your administrator account. This account will have full access to the system.</p>

@if(isset($errors['admin']))
    <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
        <strong>Error:</strong> {{ $errors['admin'] }}
    </div>
@endif

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="5">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Admin Name</label>
            <input type="text" name="admin_name" class="ng-input" placeholder="John Doe" value="{{ $data['admin']['name'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Admin Email</label>
            <input type="email" name="admin_email" class="ng-input" placeholder="admin@example.com" value="{{ $data['admin']['email'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Password</label>
            <input type="password" name="admin_password" class="ng-input" placeholder="Enter password (min 8 characters)" required minlength="8">
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Confirm Password</label>
            <input type="password" name="admin_password_confirm" class="ng-input" placeholder="Confirm password" required>
        </div>
    </div>
</form>
