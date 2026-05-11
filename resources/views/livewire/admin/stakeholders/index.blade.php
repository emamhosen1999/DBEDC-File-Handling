<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Stakeholders') }}</h2>
            <button wire:click="openCreate" class="ng-btn ng-btn-primary">+ New stakeholder</button>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-danger)]">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="border-b border-[rgba(255,255,255,0.1)]">
                        <th class="px-4 py-3 eyebrow">Code</th>
                        <th class="px-4 py-3 eyebrow">Name</th>
                        <th class="px-4 py-3 eyebrow">Description</th>
                        <th class="px-4 py-3 eyebrow"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                    @forelse($stakeholders as $s)
                        <tr class="hover:bg-[rgba(255,255,255,0.05)]">
                            <td class="px-4 py-3 text-sm">
                                <span class="ng-badge" style="background: {{ $s->color }}22; color: {{ $s->color }}">{{ $s->code }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-[var(--color-fg)]">{{ $s->name }}</td>
                            <td class="px-4 py-3 text-sm text-[var(--color-fg-muted)] truncate max-w-xs">{{ $s->description }}</td>
                            <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                <button wire:click="openEdit('{{ $s->id }}')" class="ng-btn ng-btn-sm ng-btn-ghost">Edit</button>
                                <button wire:click="delete('{{ $s->id }}')" wire:confirm="Delete this stakeholder?" class="ng-btn ng-btn-sm ng-btn-ghost text-[var(--color-danger)]">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No stakeholders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($showForm)
            <div class="glass p-4 h-fit">
                <h3 class="eyebrow mb-3">{{ $editingId ? 'Edit stakeholder' : 'New stakeholder' }}</h3>
                <form wire:submit.prevent="save" class="space-y-3">
                    <div>
                        <label class="eyebrow block mb-1">Code *</label>
                        <input type="text" wire:model="code" class="ng-input w-full">
                        @error('code') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Name *</label>
                        <input type="text" wire:model="name" class="ng-input w-full">
                        @error('name') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Color</label>
                        <input type="color" wire:model="color" class="ng-input w-full h-10">
                        @error('color') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Description</label>
                        <textarea wire:model="description" rows="2" class="ng-input w-full"></textarea>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-[var(--color-fg-muted)]">
                        <input type="checkbox" wire:model="isActive" class="rounded">
                        Active
                    </label>
                    <div class="flex gap-2">
                        <button type="button" wire:click="close" class="ng-btn ng-btn-ghost flex-1">Cancel</button>
                        <button type="submit" class="ng-btn ng-btn-primary flex-1">{{ $editingId ? 'Save' : 'Create' }}</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
