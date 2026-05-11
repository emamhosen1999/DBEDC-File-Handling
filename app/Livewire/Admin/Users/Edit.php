<?php

namespace App\Livewire\Admin\Users;

use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Edit User')]
class Edit extends Component
{
    public User $user;

    public string $name = '';
    public string $email = '';
    public string $role = 'MEMBER';
    public string $departmentId = '';
    public bool $isActive = true;
    public bool $emailNotifications = true;

    public array $roleOptions = ['ADMIN', 'MANAGER', 'MEMBER', 'VIEWER'];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->departmentId = (string) $user->department_id;
        $this->isActive = (bool) $user->is_active;
        $this->emailNotifications = (bool) $user->email_notifications;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
            'role' => 'required|in:ADMIN,MANAGER,MEMBER,VIEWER',
            'departmentId' => 'nullable|exists:departments,id',
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'department_id' => $this->departmentId ?: null,
            'is_active' => $this->isActive,
            'email_notifications' => $this->emailNotifications,
        ]);

        session()->flash('success', 'User updated.');
        return $this->redirectRoute('admin.users.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users.edit', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
