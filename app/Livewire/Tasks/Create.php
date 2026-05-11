<?php

namespace App\Livewire\Tasks;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('New Task')]
class Create extends Component
{
    public string $title = '';
    public string $description = '';
    public string $letterId = '';
    public string $assignedTo = '';
    public string $departmentId = '';
    public string $status = 'PENDING';
    public string $priority = 'MEDIUM';
    public string $dueDate = '';

    public function mount(?string $letter = null): void
    {
        Gate::authorize('create', Task::class);
        if ($letter && Letter::whereKey($letter)->exists()) {
            $this->letterId = $letter;
        }
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
        ]);

        $task = Task::create([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'letter_id' => $this->letterId ?: null,
            'assigned_to' => $this->assignedTo ?: null,
            'department_id' => $this->departmentId ?: null,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->dueDate ?: null,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Task created.');
        return $this->redirectRoute('tasks.show', $task->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.tasks.form', [
            'mode' => 'create',
            'letters' => Letter::orderByDesc('created_at')->limit(100)->get(['id', 'reference', 'title']),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
