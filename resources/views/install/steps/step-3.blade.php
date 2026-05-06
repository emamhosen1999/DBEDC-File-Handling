<h2 class="text-2xl font-bold text-white mb-4 holo-text">Root MySQL Credentials</h2>
<p class="text-white/70 mb-6">Enter your root MySQL credentials to create the database. If you already have a database created, you can skip this step by clicking "Skip" below.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="3">

    <div class="flex items-center gap-3 mb-6">
        <input type="checkbox" name="use_root_credentials" id="use_root_credentials" class="w-4 h-4 rounded" value="1">
        <label for="use_root_credentials" class="text-white/80">Use root credentials to create database</label>
    </div>

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Root MySQL Host</label>
            <input type="text" name="root_host" class="ng-input" placeholder="localhost" value="{{ $data['root_db']['host'] ?? 'localhost' }}">
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Root MySQL Port</label>
            <input type="number" name="root_port" class="ng-input" placeholder="3306" value="{{ $data['root_db']['port'] ?? '3306' }}">
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Root MySQL Username</label>
            <input type="text" name="root_user" class="ng-input" placeholder="root" value="{{ $data['root_db']['user'] ?? 'root' }}">
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">Root MySQL Password</label>
            <input type="password" name="root_pass" class="ng-input" placeholder="Enter password" value="{{ $data['root_db']['pass'] ?? '' }}">
        </div>
    </div>

    @if(isset($errors['root_db']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['root_db'] }}
        </div>
    @endif
</form>

@if(!isset($data['use_root_credentials']) || !$data['use_root_credentials'])
    <div class="ng-alert ng-alert-warning p-4 rounded-lg mb-6">
        <strong>Note:</strong> You will need to provide existing database credentials in the next step.
    </div>
@endif
