<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Tasks') }}</h2>
            <a href="{{ route('tasks.create') }}" wire:navigate class="ng-btn ng-btn-primary">+ New task</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif

    <div class="mb-6 glass p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tasks…" class="ng-input lg:col-span-2">
            <select wire:model.live="status" class="ng-select">
                <option value="">All statuses</option>
                @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ str_replace('_', ' ', $opt) }}</option>
                @endforeach
            </select>
            <select wire:model.live="priority" class="ng-select">
                <option value="">All priorities</option>
                @foreach($priorityOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
            <select wire:model.live="assigneeId" class="ng-select">
                <option value="">All assignees</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
            <label class="inline-flex items-center gap-2 text-sm text-[var(--color-fg-muted)]">
                <input type="checkbox" wire:model.live="onlyMine" class="rounded">
                Only mine
            </label>
            <button wire:click="clearFilters" class="ng-btn ng-btn-ghost ng-btn-sm">Clear filters</button>
        </div>
    </div>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($tasks as $task)
            <a href="{{ route('tasks.show', $task) }}" wire:navigate class="glass p-4 block">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="font-semibold text-[var(--color-fg)] break-words">{{ $task->title }}</div>
                        <div class="mt-1 text-xs text-[var(--color-fg-muted)]">{{ $task->assignedTo?->name ?? 'Unassigned' }}</div>
                    </div>
                    <span class="ng-badge whitespace-nowrap {{ $task->status === 'COMPLETED' ? 'ng-badge-aurora' : ($task->status === 'IN_PROGRESS' ? 'ng-badge-electric' : 'ng-badge-plasma') }}">{{ str_replace('_',' ',$task->status) }}</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-[var(--color-fg-muted)]">
                    <span>Priority: {{ $task->priority }}</span>
                    <span>{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</span>
                </div>
            </a>
        @empty
            <div class="glass p-8 text-center text-sm text-[var(--color-fg-subtle)]">No tasks found.</div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block glass overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-[rgba(255,255,255,0.1)]">
                    <th class="px-4 py-3 eyebrow">Title</th>
                    <th class="px-4 py-3 eyebrow">Assigned to</th>
                    <th class="px-4 py-3 eyebrow">Priority</th>
                    <th class="px-4 py-3 eyebrow">Status</th>
                    <th class="px-4 py-3 eyebrow">Due</th>
                    <th class="px-4 py-3 eyebrow"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                @forelse($tasks as $task)
                    <tr class="hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('tasks.show', $task) }}" wire:navigate class="hover:underline text-[var(--color-fg)]">{{ $task->title }}</a>
                            @if($task->letter)
                                <div class="text-xs text-[var(--color-fg-subtle)] mt-0.5">↪ {{ $task->letter->reference }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[var(--color-fg-muted)]">{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $task->priority }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="ng-badge {{ $task->status === 'COMPLETED' ? 'ng-badge-aurora' : ($task->status === 'IN_PROGRESS' ? 'ng-badge-electric' : 'ng-badge-plasma') }}">
                                {{ str_replace('_',' ',$task->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[var(--color-fg-muted)]">{{ $task->due_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                            <a href="{{ route('tasks.show', $task) }}" wire:navigate class="ng-btn ng-btn-sm ng-btn-ghost">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No tasks found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tasks->links() }}</div>
</div>
