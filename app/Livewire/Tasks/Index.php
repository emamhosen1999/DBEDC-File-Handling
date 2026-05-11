<?php

namespace App\Livewire\Tasks;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Tasks')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $priority = '';

    #[Url]
    public string $assigneeId = '';

    #[Url]
    public bool $onlyMine = false;

    public int $perPage = 15;

    public array $statusOptions = ['PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED', 'CANCELLED'];
    public array $priorityOptions = ['LOW', 'MEDIUM', 'HIGH', 'URGENT'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPriority(): void
    {
        $this->resetPage();
    }

    public function updatingAssigneeId(): void
    {
        $this->resetPage();
    }

    public function updatingOnlyMine(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'priority', 'assigneeId', 'onlyMine']);
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        $task = Task::findOrFail($id);
        Gate::authorize('delete', $task);
        $task->delete();
        session()->flash('success', 'Task deleted.');
    }

    public function render()
    {
        $tasks = Task::query()
            ->with(['assignedTo', 'letter'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->assigneeId, fn ($q) => $q->where('assigned_to', $this->assigneeId))
            ->when($this->onlyMine, fn ($q) => $q->where('assigned_to', auth()->id()))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
            'users' => User::where('is_active', true)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
