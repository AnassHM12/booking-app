<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $a): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isStaff()) return true; // staff see all in this demo (or scope to own staff row)
        return $a->customer_id === $user->id;
    }

    public function cancel(User $user, Appointment $a): bool
    {
        if ($user->isAdmin()) return true;
        if ($a->status === 'cancelled') return false;
        return $a->customer_id === $user->id;
    }

    public function confirm(User $user, Appointment $a): bool
    {
        return $user->isStaff();
    }
}
