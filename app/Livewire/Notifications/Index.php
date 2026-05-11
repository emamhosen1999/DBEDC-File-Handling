<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notifications')]
class Index extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'unread']) ? $filter : 'all';
        $this->resetPage();
    }

    public function markRead(string $id): void
    {
        Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->update(['is_read' => true]);
    }

    public function markAllRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        session()->flash('success', 'All notifications marked as read.');
    }

    public function destroy(string $id): void
    {
        Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();
    }

    public function render()
    {
        $notifications = Notification::query()
            ->where('user_id', auth()->id())
            ->when($this->filter === 'unread', fn ($q) => $q->where('is_read', false))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => Notification::where('user_id', auth()->id())->where('is_read', false)->count(),
        ]);
    }
}
