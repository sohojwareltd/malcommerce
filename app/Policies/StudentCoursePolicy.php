<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksPermission;

class StudentCoursePolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'studentCourses';
    }
}
