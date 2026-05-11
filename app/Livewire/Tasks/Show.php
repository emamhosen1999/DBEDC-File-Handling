<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskUpdate;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Task')]
class Show extends Component
{
    public Task $task;

    public string $comment = '';
    public string $newStatus = '';

    public array $statusOptions = ['PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED', 'CANCELLED'];

    public function mount(Task $task): void
    {
        Gate::authorize('view', $task);
        $this->task = $task->load(['assignedTo', 'letter', 'createdBy', 'department', 'updates.user']);
        $this->newStatus = $this->task->status;
    }

    public function applyTransition(): void
    {
        Gate::authorize('update', $this->task);
        $this->validate([
            'newStatus' => 'required|in:PENDING,IN_PROGRESS,REVIEW,COMPLETED,CANCELLED',
            'comment' => 'nullable|string|max:1000',
        ]);

        $old = $this->task->status;
        if ($old !== $this->newStatus || $this->comment) {
            $this->task->update([
                'status' => $this->newStatus,
                'completed_at' => $this->newStatus === 'COMPLETED'
                    ? ($this->task->completed_at ?? now())
                    : null,
            ]);

            TaskUpdate::create([
                'task_id' => $this->task->id,
                'user_id' => auth()->id(),
                'old_status' => $old,
                'new_status' => $this->newStatus,
                'comment' => $this->comment ?: null,
            ]);

            $this->task->refresh()->load('updates.user');
            $this->reset('comment');
            session()->flash('success', 'Task updated.');
        }
    }

    public function delete()
    {
        Gate::authorize('delete', $this->task);
        $this->task->delete();
        session()->flash('success', 'Task deleted.');
        return $this->redirectRoute('tasks.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.tasks.show');
    }
}
