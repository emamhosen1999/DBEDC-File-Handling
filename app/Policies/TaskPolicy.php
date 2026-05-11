<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        if (in_array($user->role, ['ADMIN', 'MANAGER'], true)) {
            return true;
        }

        return $task->created_by === $user->id
            || $task->assigned_to === $user->id
            || ($task->department_id && $task->department_id === $user->department_id);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'MANAGER', 'MEMBER'], true);
    }

    public function update(User $user, Task $task): bool
    {
        if (in_array($user->role, ['ADMIN', 'MANAGER'], true)) {
            return true;
        }
        return $task->created_by === $user->id || $task->assigned_to === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->role === 'ADMIN' || $task->created_by === $user->id;
    }
}
