<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('viewAny', Calendar::class);
    }

    public function view(User $user, Event $event): bool
    {
        // if user can see calendar then can do anything to its events (except force delete)
        return $user->can('view', $event->calendar);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function restore(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return false;
    }
}
