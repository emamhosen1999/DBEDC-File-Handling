<?php

namespace App\Livewire;

use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalLetters;
    public $totalTasks;
    public $pendingTasks;
    public $completedTasks;
    public $totalUsers;
    public $unreadNotifications;

    public function mount()
    {
        $userId = auth()->id();
        $this->totalLetters = Letter::count();
        $this->totalTasks = Task::count();
        $this->pendingTasks = Task::where('status', 'PENDING')->count();
        $this->completedTasks = Task::where('status', 'COMPLETED')->count();
        $this->totalUsers = User::count();
        $this->unreadNotifications = $userId
            ? Notification::where('user_id', $userId)->where('is_read', false)->count()
            : 0;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
