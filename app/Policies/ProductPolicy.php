<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;
use Illuminate\Database\Eloquent\Model;

class ProductPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'products';
    }

    public function builder(User $user, Product $product): bool
    {
        return $this->check($user, 'builder', $product);
    }

    public function restore(User $user, Product $product): bool
    {
        return $this->check($user, 'restore', $product);
    }
}
