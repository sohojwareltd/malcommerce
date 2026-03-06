<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksPermission;

class WithdrawalPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'withdrawals';
    }
}
