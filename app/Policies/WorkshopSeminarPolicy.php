<?php

namespace App\Policies;

use App\Models\WorkshopSeminar;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class WorkshopSeminarPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'workshopSeminars';
    }
}
