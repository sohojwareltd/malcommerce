<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Policies using this trait will check Spatie permissions.
 * Permission names are derived from resource + ability: e.g. "products.viewAny"
 */
trait ChecksPermission
{
    /**
     * Get the permission name for the given ability.
     * Override in policy if custom mapping needed.
     */
    protected function permission(string $ability): string
    {
        return $this->getResourceName() . '.' . $ability;
    }

    /**
     * Get the resource name (e.g. "products", "orders").
     * Override in policy for non-model resources.
     */
    abstract protected function getResourceName(): string;

    /**
     * Check if user has the permission for the given ability.
     */
    protected function check(User $user, string $ability, ?Model $model = null): bool
    {
        return $user->can($this->permission($ability));
    }

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'viewAny');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->check($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->check($user, 'update');
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->check($user, 'delete');
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->check($user, 'restore');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->check($user, 'forceDelete');
    }
}
