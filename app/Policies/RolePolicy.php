<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksPermission;

class RolePolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'roles';
    }
}
