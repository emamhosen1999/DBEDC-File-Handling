<?php

namespace App\Policies;

use App\Models\Letter;
use App\Models\User;

class LetterPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Letter $letter): bool
    {
        if (in_array($user->role, ['ADMIN', 'MANAGER'], true)) {
            return true;
        }

        return $letter->created_by === $user->id
            || $letter->assigned_to === $user->id
            || ($letter->department_id && $letter->department_id === $user->department_id);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'MANAGER', 'MEMBER'], true);
    }

    public function update(User $user, Letter $letter): bool
    {
        if ($user->role === 'ADMIN') {
            return true;
        }
        if ($user->role === 'MANAGER') {
            return true;
        }
        return $letter->created_by === $user->id || $letter->assigned_to === $user->id;
    }

    public function delete(User $user, Letter $letter): bool
    {
        return $user->role === 'ADMIN' || $letter->created_by === $user->id;
    }

    public function restore(User $user, Letter $letter): bool
    {
        return $user->role === 'ADMIN';
    }

    public function forceDelete(User $user, Letter $letter): bool
    {
        return $user->role === 'ADMIN';
    }
}
