<?php

namespace App\Policies;

use App\Models\BusinessHour;
use App\Models\User;

class BusinessHourPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, BusinessHour $businessHour): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, BusinessHour $businessHour): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, BusinessHour $businessHour): bool
    {
        return (bool) $user->is_admin;
    }
}
