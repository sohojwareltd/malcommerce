<?php

namespace App\Policies;

use App\Models\JobCircular;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class JobCircularPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'jobCirculars';
    }
}
