<?php

use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;

new class extends Component
{
    public $totalLetters;
    public $totalTasks;
    public $pendingTasks;
    public $completedTasks;
    public $totalUsers;
    public $unreadNotifications;

    public function mount()
    {
        $this->totalLetters = Letter::count();
        $this->totalTasks = Task::count();
        $this->pendingTasks = Task::where('status', 'PENDING')->count();
        $this->completedTasks = Task::where('status', 'COMPLETED')->count();
        $this->totalUsers = User::count();
        $this->unreadNotifications = Notification::where('user_id', auth()->id())->where('is_read', false)->count();
    }
};
?>

<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Letters</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalLetters }}</p>
                </div>
                <div class="text-electric-cyan">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Tasks</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalTasks }}</p>
                </div>
                <div class="text-plasma-magenta">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Pending Tasks</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $pendingTasks }}</p>
                </div>
                <div class="text-aurora-green">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Unread Notifications</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $unreadNotifications }}</p>
                </div>
                <div class="text-nebula-purple">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
        <h2 class="text-xl font-bold text-white mb-4">Welcome to DBEDC File Tracker</h2>
        <p class="text-gray-300">This is your dashboard. Use the navigation to manage letters, tasks, and administration.</p>
    </div>
</div>