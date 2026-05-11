<?php

namespace App\Livewire\Admin\Users;

use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $role = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function toggleActive(string $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }
        $user->update(['is_active' => ! $user->is_active]);
    }

    public function render()
    {
        $users = User::query()
            ->with('department')
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)->orWhere('email', 'like', $term);
                });
            })
            ->when($this->role, fn ($q) => $q->where('role', $this->role))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
