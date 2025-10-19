<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::SUPER_ADMIN);
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $model): bool
    {
        if ($model->trashed()) {
            return false;
        }

        return $this->viewAny($user);
    }

    public function delete(User $user, User $model): bool
    {
        // policy skipped for super admins
        if ($user->id === $model->id) {
            return false;
        }

        return $this->viewAny($user);
    }

    public function restore(User $user, User $model): bool
    {
        // policy skipped for super admins
        if ($user->id === $model->id) {
            return false;
        }

        return $this->viewAny($user);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
