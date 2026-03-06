<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    public function sales(User $user): bool
    {
        return $user->can('reports.sales');
    }
}
