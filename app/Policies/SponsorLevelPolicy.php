<?php

namespace App\Policies;

use App\Models\SponsorLevel;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class SponsorLevelPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'sponsorLevels';
    }

    public function delete(User $user, SponsorLevel $sponsorLevel): bool
    {
        return $this->check($user, 'delete', $sponsorLevel);
    }
}
