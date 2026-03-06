<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;
use Illuminate\Database\Eloquent\Model;

class OrderPolicy
{
    use ChecksPermission;

    protected function getResourceName(): string
    {
        return 'orders';
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $this->check($user, 'updateStatus', $order);
    }

    public function bulkDelete(User $user): bool
    {
        return $this->check($user, 'bulkDelete');
    }

    public function restore(User $user, Order $order): bool
    {
        return $this->check($user, 'restore', $order);
    }
}
