<?php

use App\Models\User;
use App\Models\Department;
use App\Models\Setting;

new class extends Component
{
    public $totalUsers;
    public $totalDepartments;
    public $settings;

    public function mount()
    {
        $this->totalUsers = User::count();
        $this->totalDepartments = Department::count();
        $this->settings = Setting::all();
    }
};
?>

<div class="space-y-6">
    <!-- Admin Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="text-electric-cyan">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Departments</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalDepartments }}</p>
                </div>
                <div class="text-plasma-magenta">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Management -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
        <h2 class="text-xl font-bold text-white mb-4">System Settings</h2>
        <div class="space-y-4">
            @forelse($settings as $setting)
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                    <div>
                        <p class="text-white font-medium">{{ $setting->setting_key }}</p>
                        <p class="text-gray-400 text-sm">{{ $setting->description }}</p>
                    </div>
                    <div class="text-electric-cyan">
                        <button class="hover:text-white transition">Edit</button>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">No settings configured</p>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-glass-md">
        <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('letters.index') }}" class="block p-4 bg-white/5 rounded-lg hover:bg-white/10 transition text-center">
                <p class="text-white font-medium">Manage Letters</p>
            </a>
            <a href="{{ route('tasks.index') }}" class="block p-4 bg-white/5 rounded-lg hover:bg-white/10 transition text-center">
                <p class="text-white font-medium">Manage Tasks</p>
            </a>
            <a href="#" class="block p-4 bg-white/5 rounded-lg hover:bg-white/10 transition text-center">
                <p class="text-white font-medium">Manage Users</p>
            </a>
        </div>
    </div>
</div>