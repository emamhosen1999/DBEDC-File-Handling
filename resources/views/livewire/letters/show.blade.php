<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-xs eyebrow text-[var(--color-fg-subtle)]">{{ $letter->reference }}</div>
                <h2 class="font-semibold text-xl text-white leading-tight iridescent-text break-words">{{ $letter->title }}</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $letter)
                    <a href="{{ route('letters.edit', $letter) }}" wire:navigate class="ng-btn ng-btn-secondary">Edit</a>
                @endcan
                @can('delete', $letter)
                    <button wire:click="delete" wire:confirm="Delete this letter?" class="ng-btn ng-btn-ghost text-[var(--color-danger)]">Delete</button>
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
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Status</dt><dd class="mt-1"><span class="ng-badge ng-badge-plasma">{{ str_replace('_',' ',$letter->status) }}</span></dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Priority</dt><dd class="mt-1">{{ $letter->priority }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Letter date</dt><dd class="mt-1">{{ $letter->letter_date?->format('M d, Y') ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Due date</dt><dd class="mt-1">{{ $letter->due_date?->format('M d, Y') ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Sender</dt><dd class="mt-1">{{ $letter->sender ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Recipient</dt><dd class="mt-1">{{ $letter->recipient ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Stakeholder</dt><dd class="mt-1">{{ $letter->stakeholder?->name ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Department</dt><dd class="mt-1">{{ $letter->department?->name ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Assigned to</dt><dd class="mt-1">{{ $letter->assignedTo?->name ?? '—' }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Created by</dt><dd class="mt-1">{{ $letter->createdBy?->name ?? '—' }}</dd></div>
                </dl>
                @if($letter->subject)
                    <div class="mt-4">
                        <dt class="eyebrow text-[var(--color-fg-subtle)]">Subject</dt>
                        <dd class="mt-1 text-sm">{{ $letter->subject }}</dd>
                    </div>
                @endif
                @if($letter->description)
                    <div class="mt-4">
                        <dt class="eyebrow text-[var(--color-fg-subtle)]">Description</dt>
                        <dd class="mt-1 text-sm whitespace-pre-wrap">{{ $letter->description }}</dd>
                    </div>
                @endif
            </div>

            <div class="glass p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="eyebrow">Tasks ({{ $letter->tasks->count() }})</h3>
                    @can('create', App\Models\Task::class)
                        <a href="{{ route('tasks.create', ['letter' => $letter->id]) }}" wire:navigate class="ng-btn ng-btn-sm ng-btn-secondary">+ Add task</a>
                    @endcan
                </div>
                @forelse($letter->tasks as $task)
                    <a href="{{ route('tasks.show', $task) }}" wire:navigate class="flex items-center justify-between gap-3 py-2 border-b border-[rgba(255,255,255,0.05)] last:border-0 hover:bg-[rgba(255,255,255,0.05)] transition px-2 rounded">
                        <div class="min-w-0">
                            <div class="text-sm truncate text-[var(--color-fg)]">{{ $task->title }}</div>
                            <div class="text-xs text-[var(--color-fg-muted)]">{{ $task->assignedTo?->name ?? 'Unassigned' }} · {{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</div>
                        </div>
                        <span class="ng-badge ng-badge-electric whitespace-nowrap">{{ str_replace('_',' ',$task->status) }}</span>
                    </a>
                @empty
                    <div class="text-sm text-[var(--color-fg-subtle)]">No tasks yet.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass p-6">
                <h3 class="eyebrow mb-3">Attachment</h3>
                @if($letter->file_path)
                    <div class="text-sm text-[var(--color-fg)] break-words mb-2">{{ $letter->file_name }}</div>
                    <div class="text-xs text-[var(--color-fg-muted)] mb-3">{{ number_format(($letter->file_size ?? 0) / 1024, 1) }} KB · {{ $letter->file_mime_type }}</div>
                    <a href="{{ route('letters.download', $letter) }}" class="ng-btn ng-btn-sm ng-btn-secondary">Download</a>
                @else
                    <div class="text-sm text-[var(--color-fg-subtle)]">No attachment.</div>
                @endif
            </div>

            <div class="glass p-6">
                <h3 class="eyebrow mb-3">Audit</h3>
                <dl class="text-sm space-y-2">
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Created</dt><dd>{{ $letter->created_at?->diffForHumans() }}</dd></div>
                    <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Updated</dt><dd>{{ $letter->updated_at?->diffForHumans() }}</dd></div>
                    @if($letter->completed_at)
                        <div><dt class="eyebrow text-[var(--color-fg-subtle)]">Completed</dt><dd>{{ $letter->completed_at?->diffForHumans() }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
