<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Users') }}</h2>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif

    <div class="mb-6 glass p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or email…" class="ng-input sm:col-span-2">
            <select wire:model.live="role" class="ng-select">
                <option value="">All roles</option>
                @foreach(['ADMIN','MANAGER','MEMBER','VIEWER'] as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="glass overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-[rgba(255,255,255,0.1)]">
                    <th class="px-4 py-3 eyebrow">Name</th>
                    <th class="px-4 py-3 eyebrow">Email</th>
                    <th class="px-4 py-3 eyebrow">Role</th>
                    <th class="px-4 py-3 eyebrow">Active</th>
                    <th class="px-4 py-3 eyebrow">Joined</th>
                    <th class="px-4 py-3 eyebrow"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                @forelse($users as $user)
                    <tr class="hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                        <td class="px-4 py-3 text-sm">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-sm text-[var(--color-fg-muted)]">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-sm"><span class="ng-badge ng-badge-electric">{{ $user->role }}</span></td>
                        <td class="px-4 py-3 text-sm">
                            <button wire:click="toggleActive('{{ $user->id }}')" class="ng-badge {{ $user->is_active ? 'ng-badge-aurora' : 'ng-badge-plasma' }} cursor-pointer">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-sm text-[var(--color-fg-muted)]">{{ $user->created_at?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" wire:navigate class="ng-btn ng-btn-sm ng-btn-ghost">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
