<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Activity Log') }}</h2>
    </x-slot>

    <div class="mb-6 glass p-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search activity (action, model, user)…" class="ng-input w-full">
    </div>

    <div class="glass overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-[rgba(255,255,255,0.1)]">
                    <th class="px-4 py-3 eyebrow">When</th>
                    <th class="px-4 py-3 eyebrow">User</th>
                    <th class="px-4 py-3 eyebrow">Action</th>
                    <th class="px-4 py-3 eyebrow">Entity</th>
                    <th class="px-4 py-3 eyebrow">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                @forelse($activities as $a)
                    <tr class="hover:bg-[rgba(255,255,255,0.05)]">
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-[var(--color-fg-muted)]" title="{{ $a->created_at?->format('Y-m-d H:i:s') }}">{{ $a->created_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-sm">{{ $a->user?->name ?? 'System' }}</td>
                        <td class="px-4 py-3 text-sm"><span class="ng-badge ng-badge-electric">{{ $a->action }}</span></td>
                        <td class="px-4 py-3 text-sm text-[var(--color-fg-muted)]">{{ $a->entity_type ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-[var(--color-fg-muted)] break-words">{{ $a->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No activity.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $activities->links() }}</div>
</div>
