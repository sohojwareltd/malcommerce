<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use App\Policies\Concerns\ChecksPermission;

class VideoPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'videos';
    }

    public function restore(User $user, Video $video): bool
    {
        return $this->check($user, 'restore', $video);
    }
}
