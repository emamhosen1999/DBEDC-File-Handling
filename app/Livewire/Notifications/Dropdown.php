<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class Dropdown extends Component
{
    public int $unread = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('notifications.refresh')]
    public function refreshCount(): void
    {
        $this->unread = auth()->check()
            ? Notification::where('user_id', auth()->id())->where('is_read', false)->count()
            : 0;
    }

    public function render()
    {
        $recent = auth()->check()
            ? Notification::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();

        return view('livewire.notifications.dropdown', [
            'recent' => $recent,
        ]);
    }
}
