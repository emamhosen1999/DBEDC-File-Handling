<h2 class="text-2xl font-bold text-white mb-4 holo-text">System Requirements</h2>

@if(isset($data['requirements']) && is_array($data['requirements']) && !empty($data['requirements']))
    @if(isset($data['requirements_passed']) && $data['requirements_passed'])
        <div class="ng-alert ng-alert-success p-4 rounded-lg mb-6">
            <strong>All requirements are met!</strong>
        </div>
    @else
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Some requirements are not met. Please fix them before continuing.</strong>
        </div>
    @endif

    <div class="space-y-3 mb-6">
        @foreach($data['requirements'] as $req)
            @if(is_array($req) && isset($req['status']))
                <div class="flex items-center justify-between p-3 rounded-lg {{ $req['status'] ? 'bg-green-500/10' : 'bg-red-500/10' }}">
                    <span class="text-white/80">
                        <strong>{{ $req['name'] ?? 'Unknown' }}</strong>
                        <span class="text-white/50 text-xs ml-2">Required: {{ $req['required'] ?? 'N/A' }} | Current: {{ $req['current'] ?? 'N/A' }}</span>
                    </span>
                    @if($req['status'])
                        <span class="badge ng-badge ng-badge-success">Passed</span>
                    @else
                        <span class="badge ng-badge ng-badge-danger">Failed</span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
@endif

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="2">
</form>
