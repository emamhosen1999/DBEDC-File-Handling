<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight iridescent-text break-words">{{ $task->title }}</h2>
            <div class="flex flex-wrap gap-2">
                @can('update', $task)
                    <a href="{{ route('tasks.edit', $task) }}" wire:navigate class="ng-btn ng-btn-secondary">Edit</a>
                @endcan
                @can('delete', $task)
                    <button wire:click="delete" wire:confirm="Delete this task?" class="ng-btn ng-btn-ghost text-[var(--color-danger)]">Delete</button>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass p-6">
                <h3 class="eyebrow mb-3">Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Status</dt><dd class="mt-1"><span class="ng-badge {{ $task->status === 'COMPLETED' ? 'ng-badge-aurora' : ($task->status === 'IN_PROGRESS' ? 'ng-badge-electric' : 'ng-badge-plasma') }}">{{ str_replace('_',' ',$task->status) }}</span></dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Priority</dt><dd class="mt-1">{{ $task->priority }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Assigned to</dt><dd class="mt-1">{{ $task->assignedTo?->name ?? 'Unassigned' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Department</dt><dd class="mt-1">{{ $task->department?->name ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Due date</dt><dd class="mt-1">{{ $task->due_date?->format('M d, Y') ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Created by</dt><dd class="mt-1">{{ $task->createdBy?->name ?? '—' }}</dd></div>
                </dl>
                @if($task->description)
                    <div class="mt-4">
                        <dt class="eyebrow text-[var(--color-fg-subtle)]">Description</dt>
                        <dd class="mt-1 text-sm whitespace-pre-wrap">{{ $task->description }}</dd>
                    </div>
                @endif
                @if($task->letter)
                    <div class="mt-4">
                        <dt class="eyebrow text-[var(--color-fg-subtle)]">Linked letter</dt>
                        <dd class="mt-1 text-sm">
                            <a href="{{ route('letters.show', $task->letter) }}" wire:navigate class="hover:underline">
                                {{ $task->letter->reference }} — {{ $task->letter->title }}
                            </a>
                        </dd>
                    </div>
                @endif
            </div>

            <div class="glass p-6">
                <h3 class="eyebrow mb-3">Activity</h3>
                @forelse($task->updates->sortByDesc('created_at') as $u)
                    <div class="py-3 border-b border-[rgba(255,255,255,0.05)] last:border-0">
                        <div class="text-sm text-[var(--color-fg)]">
                            <strong>{{ $u->user?->name ?? 'Someone' }}</strong>
                            @if($u->old_status && $u->new_status && $u->old_status !== $u->new_status)
                                changed status from <span class="ng-badge ng-badge-plasma">{{ str_replace('_',' ',$u->old_status) }}</span>
                                to <span class="ng-badge ng-badge-electric">{{ str_replace('_',' ',$u->new_status) }}</span>
                            @else
                                added a comment
                            @endif
                        </div>
                        @if($u->comment)
                            <div class="mt-1 text-sm text-[var(--color-fg-muted)] whitespace-pre-wrap">{{ $u->comment }}</div>
                        @endif
                        <div class="text-xs text-[var(--color-fg-subtle)] mt-1">{{ $u->created_at?->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="text-sm text-[var(--color-fg-subtle)]">No activity yet.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            @can('update', $task)
                <div class="glass p-6">
                    <h3 class="eyebrow mb-3">Quick update</h3>
                    <form wire:submit.prevent="applyTransition" class="space-y-3">
                        <div>
                            <label class="eyebrow block mb-1">Status</label>
                            <select wire:model="newStatus" class="ng-select w-full">
                                @foreach($statusOptions as $s)
                                    <option value="{{ $s }}">{{ str_replace('_',' ',$s) }}</option>
                                @endforeach
                            </select>
                            @error('newStatus') <div class="text-sm text-[var(--color-danger)] mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="eyebrow block mb-1">Comment (optional)</label>
                            <textarea wire:model="comment" rows="3" class="ng-input w-full"></textarea>
                        </div>
                        <button type="submit" class="ng-btn ng-btn-primary w-full">Update</button>
                    </form>
                </div>
            @endcan

            <div class="glass p-6">
                <h3 class="eyebrow mb-3">Audit</h3>
                <dl class="text-sm space-y-2">
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Created</dt><dd>{{ $task->created_at?->diffForHumans() }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Updated</dt><dd>{{ $task->updated_at?->diffForHumans() }}</dd></div>
                    @if($task->completed_at)
                        <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Completed</dt><dd>{{ $task->completed_at?->diffForHumans() }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
