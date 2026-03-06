<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
    public function view(User $user): bool
    {
        return $user->can('profile.view');
    }

    public function update(User $user): bool
    {
        return $user->can('profile.update');
    }

    public function changePassword(User $user): bool
    {
        return $user->can('profile.changePassword');
    }
}
