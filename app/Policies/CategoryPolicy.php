<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class CategoryPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'categories';
    }

    public function restore(User $user, Category $category): bool
    {
        return $this->check($user, 'restore', $category);
    }
}
