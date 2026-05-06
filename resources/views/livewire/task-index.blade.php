<div>
    <div class="mb-6 flex gap-4 glass p-4">
        <input type="text" 
               wire:model.live="search" 
               placeholder="Search tasks..." 
               class="ng-input">
        
        <select wire:model.live="statusFilter" class="ng-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="glass overflow-hidden">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-[rgba(255,255,255,0.1)]">
                    <th class="px-6 py-4 eyebrow">Title</th>
                    <th class="px-6 py-4 eyebrow">Assigned To</th>
                    <th class="px-6 py-4 eyebrow">Due Date</th>
                    <th class="px-6 py-4 eyebrow">Status</th>
                    <th class="px-6 py-4 eyebrow">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                @forelse($tasks as $task)
                    <tr class="hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                        <td class="px-6 py-4 text-sm">{{ $task->title }}</td>
                        <td class="px-6 py-4 text-sm text-[var(--color-fg-subtle)]">{{ $task->assigned_to ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-fg-subtle)]">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="ng-badge 
                                {{ $task->status === 'completed' ? 'ng-badge-aurora' : 
                                   ($task->status === 'in_progress' ? 'ng-badge-electric' : 'ng-badge-plasma') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('tasks.show', $task->id) }}" class="ng-btn ng-btn-sm ng-btn-ghost">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No tasks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</div>
