<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;

class TaskIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';

    public function render()
    {
        $tasks = Task::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        })->when($this->statusFilter, function ($query) {
            $query->where('status', $this->statusFilter);
        })->paginate($this->perPage);

        return view('livewire.task-index', [
            'tasks' => $tasks,
        ]);
    }
}
