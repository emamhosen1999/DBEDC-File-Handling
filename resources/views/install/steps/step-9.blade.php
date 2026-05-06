<h2 class="text-2xl font-bold text-white mb-4 holo-text">WeChat OAuth Configuration</h2>
<p class="text-white/70 mb-6">Configure WeChat OAuth for authentication. You need to create a WeChat Open Platform application.</p>

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="9">

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">WeChat App ID</label>
            <input type="text" name="wechat_app_id" class="ng-input" placeholder="Enter your WeChat App ID" value="{{ $data['wechat_oauth']['app_id'] ?? '' }}" required>
        </div>
        <div>
            <label class="block text-white/80 mb-2 text-sm font-medium">WeChat App Secret</label>
            <input type="text" name="wechat_app_secret" class="ng-input" placeholder="Enter your WeChat App Secret" value="{{ $data['wechat_oauth']['app_secret'] ?? '' }}" required>
        </div>
    </div>

    <div class="ng-alert ng-alert-warning p-4 rounded-lg mb-6">
        <strong>Important:</strong> Make sure to add http://localhost:8000/auth/wechat/callback as an authorized redirect URI in your WeChat Open Platform settings.
    </div>

    @if(isset($errors['wechat_oauth']))
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Error:</strong> {{ $errors['wechat_oauth'] }}
        </div>
    @endif
</form>
