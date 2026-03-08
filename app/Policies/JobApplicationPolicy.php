<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class JobApplicationPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'jobApplications';
    }
}
