<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class SponsorPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'sponsors';
    }

    public function restore(User $user, User $sponsor): bool
    {
        return $this->check($user, 'restore', $sponsor);
    }
}
