<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class ExamPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'exams';
    }

    public function restore(User $user, Exam $exam): bool
    {
        return $this->check($user, 'restore', $exam);
    }
}
