<h2 class="text-2xl font-bold text-white mb-4 holo-text">Welcome to DBEDC File Tracker</h2>
<p class="text-white/70 mb-6">This wizard will guide you through the complete installation process. All configuration is mandatory to ensure the application runs properly without requiring any technical knowledge or cPanel access.</p>
<p class="text-white font-semibold mb-3">You will need to provide:</p>
<ul class="ml-6 text-white/60 space-y-2 mb-8">
    <li>Root MySQL credentials (to create database) OR existing database credentials</li>
    <li>Admin account details</li>
    <li>Application URL and branding information</li>
    <li>Google OAuth credentials (for authentication)</li>
    <li>WeChat OAuth credentials (for authentication)</li>
    <li>Email server configuration (for notifications)</li>
</ul>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="1">
</form>

