<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, Inventory $inventory): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, Inventory $inventory): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, Inventory $inventory): bool
    {
        return (bool) $user->is_admin;
    }
}
