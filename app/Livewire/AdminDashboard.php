<?php

namespace App\Livewire;

use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $totalLetters;
    public $totalTasks;
    public $pendingTasks;
    public $completedTasks;
    public $totalUsers;
    public $activeUsers;

    public function mount()
    {
        $this->totalLetters = Letter::count();
        $this->totalTasks = Task::count();
        $this->pendingTasks = Task::where('status', 'pending')->count();
        $this->completedTasks = Task::where('status', 'completed')->count();
        $this->totalUsers = User::count();
        $this->activeUsers = User::where('is_active', true)->count();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
