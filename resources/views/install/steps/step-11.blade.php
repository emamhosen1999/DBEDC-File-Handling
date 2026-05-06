<h2 class="text-2xl font-bold text-white mb-4 holo-text">File Permissions</h2>
<p class="text-white/70 mb-6">The following directories need to be writable by the web server.</p>

@if(isset($data['permissions']))
    @php
        $allWritable = collect($data['permissions'])->every('writable');
    @endphp

    @if($allWritable)
        <div class="ng-alert ng-alert-success p-4 rounded-lg mb-6">
            <strong>All directories are writable!</strong>
        </div>
    @else
        <div class="ng-alert ng-alert-danger p-4 rounded-lg mb-6">
            <strong>Some directories are not writable. Please fix the permissions manually.</strong>
        </div>
    @endif

    <div class="space-y-3 mb-6">
        @foreach($data['permissions'] as $name => $perm)
            <div class="flex items-center justify-between p-3 rounded-lg {{ $perm['writable'] ? 'bg-green-500/10' : 'bg-red-500/10' }}">
                <span class="text-white/80">
                    <strong>{{ $perm['path'] }}</strong>
                    @if($perm['exists'])
                        <span class="text-white/50 text-xs ml-2">Exists</span>
                    @else
                        <span class="text-white/50 text-xs ml-2">Will be created</span>
                    @endif
                </span>
                @if($perm['writable'])
                    <span class="badge ng-badge ng-badge-success">Writable</span>
                @else
                    <span class="badge ng-badge ng-badge-danger">Not Writable</span>
                @endif
            </div>
        @endforeach
    </div>

    @if(!$allWritable)
        <div class="ng-alert ng-alert-warning p-4 rounded-lg mb-6">
            <strong>Manual Fix Required:</strong>
            <p class="mt-2 text-sm">Run the following commands in your terminal:</p>
            <pre class="mt-2 text-xs bg-black/30 p-3 rounded overflow-x-auto">
@foreach($data['permissions'] as $perm)
    @if(!$perm['writable'])
chmod {{ $perm['path'] }} 755
    @endif
@endforeach
            </pre>
        </div>
    @endif
@else
    <p class="text-white/70 mb-6">Checking file permissions...</p>
@endif

<form action="{{ route('install.process') }}" method="POST" id="step-form">
    @csrf
    <input type="hidden" name="step" value="11">
</form>
