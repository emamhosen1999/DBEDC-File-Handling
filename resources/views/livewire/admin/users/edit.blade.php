<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Edit User') }}: {{ $user->name }}</h2>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="save" class="glass p-6 max-w-2xl mx-auto space-y-5">
        <div>
            <label class="eyebrow block mb-1">Name *</label>
            <input type="text" wire:model="name" class="ng-input w-full">
            @error('name') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="eyebrow block mb-1">Email *</label>
            <input type="email" wire:model="email" class="ng-input w-full">
            @error('email') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="eyebrow block mb-1">Role *</label>
                <select wire:model="role" class="ng-select w-full">
                    @foreach(['ADMIN','MANAGER','MEMBER','VIEWER'] as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="eyebrow block mb-1">Department</label>
                <select wire:model="departmentId" class="ng-select w-full">
                    <option value="">—</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="eyebrow block mb-1">Phone</label>
                <input type="text" wire:model="phone" class="ng-input w-full">
            </div>
            <div>
                <label class="eyebrow block mb-1">Job title</label>
                <input type="text" wire:model="jobTitle" class="ng-input w-full">
            </div>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-[var(--color-fg-muted)]">
            <input type="checkbox" wire:model="isActive" class="rounded">
            Active
        </label>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}" wire:navigate class="ng-btn ng-btn-ghost">Cancel</a>
            <button type="submit" class="ng-btn ng-btn-primary">Save</button>
        </div>
    </form>
</div>
