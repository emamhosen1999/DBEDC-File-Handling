<?php

namespace App\Livewire\Tasks;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Edit Task')]
class Edit extends Component
{
    public Task $task;

    public string $title = '';
    public string $description = '';
    public string $letterId = '';
    public string $assignedTo = '';
    public string $departmentId = '';
    public string $status = 'PENDING';
    public string $priority = 'MEDIUM';
    public string $dueDate = '';
    public string $comment = '';

    public function mount(Task $task): void
    {
        Gate::authorize('update', $task);
        $this->task = $task;
        $this->title = $task->title;
        $this->description = (string) $task->description;
        $this->letterId = (string) $task->letter_id;
        $this->assignedTo = (string) $task->assigned_to;
        $this->departmentId = (string) $task->department_id;
        $this->status = $task->status;
        $this->priority = $task->priority;
        $this->dueDate = $task->due_date?->toDateString() ?? '';
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'letterId' => 'nullable|exists:letters,id',
            'assignedTo' => 'nullable|exists:users,id',
            'departmentId' => 'nullable|exists:departments,id',
            'status' => 'required|in:PENDING,IN_PROGRESS,REVIEW,COMPLETED,CANCELLED',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'dueDate' => 'nullable|date',
            'comment' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $this->task->status;

        $this->task->update([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'letter_id' => $this->letterId ?: null,
            'assigned_to' => $this->assignedTo ?: null,
            'department_id' => $this->departmentId ?: null,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->dueDate ?: null,
            'completed_at' => $this->status === 'COMPLETED'
                ? ($this->task->completed_at ?? now())
                : null,
        ]);

        if ($oldStatus !== $this->status || $this->comment) {
            TaskUpdate::create([
                'task_id' => $this->task->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => $this->status,
                'comment' => $this->comment ?: null,
            ]);
        }

        session()->flash('success', 'Task updated.');
        return $this->redirectRoute('tasks.show', $this->task->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.tasks.form', [
            'mode' => 'edit',
            'task' => $this->task,
            'letters' => Letter::orderByDesc('created_at')->limit(100)->get(['id', 'reference', 'title']),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
