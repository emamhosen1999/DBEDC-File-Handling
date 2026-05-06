<h2 class="text-2xl font-bold text-white mb-4 holo-text">Ready to Install</h2>
<div class="ng-alert ng-alert-warning p-4 rounded-lg mb-6">
    <strong>Warning:</strong> This will create/modify your database and configuration files. Make sure you have reviewed all settings before proceeding.
</div>

<h3 class="text-xl font-bold text-white mb-4 mt-8">Configuration Summary:</h3>
<div class="space-y-2 mb-8 text-white/70">
    @if(isset($data['database']))
        <div><strong>Database:</strong> {{ $data['database']['host'] }}/{{ $data['database']['name'] }}</div>
    @endif
    @if(isset($data['admin']))
        <div><strong>Admin Email:</strong> {{ $data['admin']['email'] }}</div>
    @endif
    @if(isset($data['app']))
        <div><strong>App URL:</strong> {{ $data['app']['url'] }}</div>
    @endif
    @if(isset($data['branding']))
        <div><strong>Company Name:</strong> {{ $data['branding']['company_name'] }}</div>
    @endif
</div>

@if(isset($errors['installation']))
    <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
        <strong>Error:</strong> {{ $errors['installation'] }}
    </div>
@endif

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="12">
</form>
