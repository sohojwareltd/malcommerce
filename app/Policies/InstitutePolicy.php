<?php

namespace App\Policies;

use App\Models\Institute;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class InstitutePolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'institutes';
    }

    public function restore(User $user, Institute $institute): bool
    {
        return $this->check($user, 'restore', $institute);
    }
}
