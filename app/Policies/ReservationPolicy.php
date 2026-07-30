<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return (bool) $user->is_admin;
    }
}
