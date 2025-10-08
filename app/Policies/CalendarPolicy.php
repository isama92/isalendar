<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\User;

class CalendarPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Calendar $calendar): bool
    {
        return $calendar->connectors()->whereBelongsTo($user)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Calendar $calendar): bool
    {
        return $this->view($user, $calendar);
    }

    public function delete(User $user, Calendar $calendar): bool
    {
        return $this->view($user, $calendar);
    }

    public function restore(User $user, Calendar $calendar): bool
    {
        return $this->view($user, $calendar);
    }

    public function forceDelete(User $user, Calendar $calendar): bool
    {
        return false;
    }
}
