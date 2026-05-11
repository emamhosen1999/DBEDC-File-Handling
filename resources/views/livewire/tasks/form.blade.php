<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">
            {{ $mode === 'create' ? __('New Task') : __('Edit Task') }}
        </h2>
    </x-slot>

    <form wire:submit.prevent="save" class="glass p-6 max-w-3xl mx-auto space-y-5">
        <div>
            <label class="eyebrow block mb-1">Title *</label>
            <input type="text" wire:model="title" class="ng-input w-full">
            @error('title') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="eyebrow block mb-1">Description</label>
            <textarea wire:model="description" rows="4" class="ng-input w-full"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="eyebrow block mb-1">Linked letter</label>
                <select wire:model="letterId" class="ng-select w-full">
                    <option value="">—</option>
                    @foreach($letters as $l)
                        <option value="{{ $l->id }}">{{ $l->reference }} — {{ \Illuminate\Support\Str::limit($l->title, 40) }}</option>
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
                <label class="eyebrow block mb-1">Due date</label>
                <input type="date" wire:model="dueDate" class="ng-input w-full">
            </div>
            <div>
                <label class="eyebrow block mb-1">Status *</label>
                <select wire:model="status" class="ng-select w-full">
                    @foreach(['PENDING','IN_PROGRESS','REVIEW','COMPLETED','CANCELLED'] as $s)
                        <option value="{{ $s }}">{{ str_replace('_',' ',$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="eyebrow block mb-1">Priority *</label>
                <select wire:model="priority" class="ng-select w-full">
                    @foreach(['LOW','MEDIUM','HIGH','URGENT'] as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($mode === 'edit')
            <div>
                <label class="eyebrow block mb-1">Comment (optional, will be added to task history)</label>
                <textarea wire:model="comment" rows="2" class="ng-input w-full"></textarea>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <a href="{{ $mode === 'edit' ? route('tasks.show', $task->id) : route('tasks.index') }}" wire:navigate class="ng-btn ng-btn-ghost">Cancel</a>
            <button type="submit" class="ng-btn ng-btn-primary">
                {{ $mode === 'create' ? 'Create task' : 'Save changes' }}
            </button>
        </div>
    </form>
</div>
