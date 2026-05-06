<?php

use App\Models\Task;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';

    public function render()
    {
        $tasks = Task::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                $query->where('priority', $this->priorityFilter);
            })
            ->with(['letter', 'assignedTo', 'department'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.task-index', [
            'tasks' => $tasks,
        ]);
    }
};
?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Search tasks..."
                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-electric-cyan"
            >
            <select
                wire:model.live="statusFilter"
                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-cyan"
            >
                <option value="">All Statuses</option>
                <option value="PENDING">Pending</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="COMPLETED">Completed</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
            <select
                wire:model.live="priorityFilter"
                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-electric-cyan"
            >
                <option value="">All Priorities</option>
                <option value="LOW">Low</option>
                <option value="MEDIUM">Medium</option>
                <option value="HIGH">High</option>
                <option value="URGENT">Urgent</option>
            </select>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden shadow-glass-md">
        <table class="w-full">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Letter</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Assigned To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse($tasks as $task)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $task->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $task->letter?->title ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $task->status === 'COMPLETED' ? 'bg-aurora-green/20 text-aurora-green' : 'bg-electric-cyan/20 text-electric-cyan' }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $task->priority === 'URGENT' ? 'bg-plasma-magenta/20 text-plasma-magenta' : 'bg-nebula-purple/20 text-nebula-purple' }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $task->assignedTo?->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $task->due_date?->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button class="text-electric-cyan hover:text-white transition">View</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-400">No tasks found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="px-6 py-4 border-t border-white/10 flex items-center justify-between">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>