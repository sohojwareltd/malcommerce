<?php

namespace App\Policies;

use App\Models\WorkshopEnrollment;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class WorkshopEnrollmentPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'workshopEnrollments';
    }
}
