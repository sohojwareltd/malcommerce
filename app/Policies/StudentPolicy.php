<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class StudentPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'students';
    }

    public function restore(User $user, Student $student): bool
    {
        return $this->check($user, 'restore', $student);
    }

    public function downloadDocuments(User $user, Student $student): bool
    {
        return $this->check($user, 'view', $student);
    }
}
