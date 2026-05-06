<h2 class="text-2xl font-bold text-white mb-4 holo-text">Database Configuration</h2>
<p class="text-white/70 mb-6">Enter the database credentials for your application. If you used root credentials in the previous step, the database will be created automatically.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="4">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Database Host</label>
            <input type="text" name="db_host" class="ng-input" placeholder="localhost" value="{{ $data['database']['host'] ?? 'localhost' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Database Port</label>
            <input type="number" name="db_port" class="ng-input" placeholder="3306" value="{{ $data['database']['port'] ?? '3306' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Database Name</label>
            <input type="text" name="db_name" class="ng-input" placeholder="dbedc_file_handling" value="{{ $data['database']['name'] ?? 'dbedc_file_handling' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Database Username</label>
            <input type="text" name="db_user" class="ng-input" placeholder="dbedc_user" value="{{ $data['database']['user'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Database Password</label>
            <input type="password" name="db_pass" class="ng-input" placeholder="Enter password" value="{{ $data['database']['pass'] ?? '' }}">
        </div>
    </div>

    @if(isset($errors['db']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['db'] }}
        </div>
    @endif

    @if(isset($errors['db_create']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['db_create'] }}
        </div>
    @endif
</form>
