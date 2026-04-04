<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksPermission;
use Illuminate\Database\Eloquent\Model;

class UserPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'users';
    }

    public function restore(User $user, Model $model): bool
    {
        if ($model instanceof User && $model->role === 'sponsor') {
            return $user->can('sponsors.restore');
        }

        return $this->check($user, 'restore', $model);
    }

    public function forceDelete(User $user, User $model): bool
    {
        if ($model->role === 'sponsor') {
            return $user->can('sponsors.forceDelete');
        }

        return $this->check($user, 'forceDelete', $model);
    }
}
