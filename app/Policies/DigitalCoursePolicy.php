<?php

namespace App\Policies;

use App\Models\DigitalCourse;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class DigitalCoursePolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'digital_courses';
    }

    public function restore(User $user, DigitalCourse $digitalCourse): bool
    {
        return $this->check($user, 'restore', $digitalCourse);
    }
}
