<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class ExpensePolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'expenses';
    }

    public function restore(User $user, Expense $expense): bool
    {
        return $this->check($user, 'restore', $expense);
    }
}
