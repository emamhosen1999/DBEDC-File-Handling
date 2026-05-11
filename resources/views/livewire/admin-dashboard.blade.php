<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass tilt-card">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium eyebrow truncate">Total Letters</dt>
                <dd class="mt-1 text-3xl font-semibold holo-text">{{ $totalLetters }}</dd>
            </div>
        </div>
        <div class="glass tilt-card">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium eyebrow truncate">Total Tasks</dt>
                <dd class="mt-1 text-3xl font-semibold holo-text">{{ $totalTasks }}</dd>
            </div>
        </div>
        <div class="glass tilt-card">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium eyebrow truncate">Pending Tasks</dt>
                <dd class="mt-1 text-3xl font-semibold holo-text">{{ $pendingTasks }}</dd>
            </div>
        </div>
        <div class="glass tilt-card">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium eyebrow truncate">Completed Tasks</dt>
                <dd class="mt-1 text-3xl font-semibold holo-text">{{ $completedTasks }}</dd>
            </div>
        </div>
        <div class="glass tilt-card">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium eyebrow truncate">Total Users</dt>
                <dd class="mt-1 text-3xl font-semibold holo-text">{{ $totalUsers }}</dd>
            </div>
        </div>
        <div class="glass tilt-card">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium eyebrow truncate">Active Users</dt>
                <dd class="mt-1 text-3xl font-semibold holo-text">{{ $activeUsers }}</dd>
            </div>
        </div>
    </div>

    <h3 class="eyebrow mb-3 text-white">Manage</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" wire:navigate class="glass p-5 hover:bg-white/5 transition">
            <div class="text-base font-semibold text-[var(--color-fg)]">Users</div>
            <div class="mt-1 text-sm text-[var(--color-fg-muted)]">Manage accounts, roles, and activation status.</div>
        </a>
        <a href="{{ route('admin.departments.index') }}" wire:navigate class="glass p-5 hover:bg-white/5 transition">
            <div class="text-base font-semibold text-[var(--color-fg)]">Departments</div>
            <div class="mt-1 text-sm text-[var(--color-fg-muted)]">Define org structure with hierarchical departments.</div>
        </a>
        <a href="{{ route('admin.stakeholders.index') }}" wire:navigate class="glass p-5 hover:bg-white/5 transition">
            <div class="text-base font-semibold text-[var(--color-fg)]">Stakeholders</div>
            <div class="mt-1 text-sm text-[var(--color-fg-muted)]">External parties for letters (donors, agencies, etc.).</div>
        </a>
        <a href="{{ route('admin.activities.index') }}" wire:navigate class="glass p-5 hover:bg-white/5 transition">
            <div class="text-base font-semibold text-[var(--color-fg)]">Activity Log</div>
            <div class="mt-1 text-sm text-[var(--color-fg-muted)]">Audit trail of all changes across the system.</div>
        </a>
    </div>
</div>
