<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Departments') }}</h2>
            <button wire:click="openCreate" class="ng-btn ng-btn-primary">+ New department</button>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-danger)]">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass p-4">
            <h3 class="eyebrow mb-3">All departments</h3>
            <div class="space-y-1">
                @forelse($departments as $d)
                    <div class="flex items-center justify-between gap-3 px-3 py-2 rounded hover:bg-white/5">
                        <div class="min-w-0">
                            <div class="text-sm text-[var(--color-fg)] truncate">
                                @if($d->parent_id)<span class="text-[var(--color-fg-subtle)]">↳ </span>@endif
                                {{ $d->name }}
                                @if(! $d->is_active)<span class="ng-badge ng-badge-plasma ml-2">Inactive</span>@endif
                            </div>
                            @if($d->parent)
                                <div class="text-xs text-[var(--color-fg-subtle)]">Parent: {{ $d->parent->name }}</div>
                            @endif
                            @if($d->manager)
                                <div class="text-xs text-[var(--color-fg-subtle)]">Manager: {{ $d->manager->name }}</div>
                            @endif
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="openEdit('{{ $d->id }}')" class="ng-btn ng-btn-sm ng-btn-ghost">Edit</button>
                            <button wire:click="delete('{{ $d->id }}')" wire:confirm="Delete this department?" class="ng-btn ng-btn-sm ng-btn-ghost text-[var(--color-danger)]">Delete</button>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-[var(--color-fg-subtle)] p-4">No departments yet.</div>
                @endforelse
            </div>
        </div>

        @if($showForm)
            <div class="glass p-4 h-fit">
                <h3 class="eyebrow mb-3">{{ $editingId ? 'Edit department' : 'New department' }}</h3>
                <form wire:submit.prevent="save" class="space-y-3">
                    <div>
                        <label class="eyebrow block mb-1">Name *</label>
                        <input type="text" wire:model="name" class="ng-input w-full">
                        @error('name') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Description</label>
                        <textarea wire:model="description" rows="2" class="ng-input w-full"></textarea>
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Parent</label>
                        <select wire:model="parentId" class="ng-select w-full">
                            <option value="">— (root) —</option>
                            @foreach($parents as $d)
                                @if($editingId !== $d->id)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Manager</label>
                        <select wire:model="managerId" class="ng-select w-full">
                            <option value="">—</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="eyebrow block mb-1">Display order</label>
                        <input type="number" wire:model="displayOrder" class="ng-input w-full">
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
