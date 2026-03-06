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
        return $this->check($user, 'restore', $model);
    }
}
