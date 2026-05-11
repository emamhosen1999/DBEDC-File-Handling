<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">
            {{ $mode === 'create' ? __('New Letter') : __('Edit Letter') }}
        </h2>
    </x-slot>

    <form wire:submit.prevent="save" class="glass p-6 max-w-4xl mx-auto space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="eyebrow block mb-1">Reference *</label>
                <input type="text" wire:model="reference" class="ng-input w-full">
                @error('reference') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="eyebrow block mb-1">Letter Date *</label>
                <input type="date" wire:model="letterDate" class="ng-input w-full">
                @error('letterDate') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div>
            <label class="eyebrow block mb-1">Title *</label>
            <input type="text" wire:model="title" class="ng-input w-full">
            @error('title') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="eyebrow block mb-1">Subject</label>
            <input type="text" wire:model="subject" class="ng-input w-full">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="eyebrow block mb-1">Sender</label>
                <input type="text" wire:model="sender" class="ng-input w-full">
            </div>
            <div>
                <label class="eyebrow block mb-1">Recipient</label>
                <input type="text" wire:model="recipient" class="ng-input w-full">
            </div>
        </div>

        <div>
            <label class="eyebrow block mb-1">Description</label>
            <textarea wire:model="description" rows="4" class="ng-input w-full"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="eyebrow block mb-1">Priority *</label>
                <select wire:model="priority" class="ng-select w-full">
                    @foreach(['LOW','MEDIUM','HIGH','URGENT'] as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="eyebrow block mb-1">Status *</label>
                <select wire:model="status" class="ng-select w-full">
                    @foreach(['DRAFT','PENDING','IN_PROGRESS','REVIEW','COMPLETED','ARCHIVED'] as $s)
                        <option value="{{ $s }}">{{ str_replace('_',' ',$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="eyebrow block mb-1">Due Date</label>
                <input type="date" wire:model="dueDate" class="ng-input w-full">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="eyebrow block mb-1">Stakeholder *</label>
                <select wire:model="stakeholderId" class="ng-select w-full">
                    <option value="">Select…</option>
                    @foreach($stakeholders as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                    @endforeach
                </select>
                @error('stakeholderId') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
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
                <label class="eyebrow block mb-1">Assigned to</label>
                <select wire:model="assignedTo" class="ng-select w-full">
                    <option value="">—</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="eyebrow block mb-1">Attachment (PDF/image/doc, max 20 MB)</label>
            <input type="file" wire:model="attachment" class="ng-input w-full">
            <div wire:loading wire:target="attachment" class="text-xs text-[var(--color-fg-muted)] mt-1">Uploading…</div>
            @error('attachment') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
            @if($mode === 'edit' && $letter->file_name ?? false)
                <div class="text-xs text-[var(--color-fg-muted)] mt-1">Current: {{ $letter->file_name }}</div>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <a href="{{ $mode === 'edit' ? route('letters.show', $letter->id) : route('letters.index') }}" wire:navigate class="ng-btn ng-btn-ghost">Cancel</a>
            <button type="submit" class="ng-btn ng-btn-primary">
                {{ $mode === 'create' ? 'Create letter' : 'Save changes' }}
            </button>
        </div>
    </form>
</div>
