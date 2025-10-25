<?php

namespace App\Policies;

use App\Models\EventTemplate;
use App\Models\User;

class EventTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EventTemplate $eventTemplate): bool
    {
        return $eventTemplate->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, EventTemplate $eventTemplate): bool
    {
        return $this->view($user, $eventTemplate);
    }

    public function delete(User $user, EventTemplate $eventTemplate): bool
    {
        return $this->view($user, $eventTemplate);
    }

    public function restore(User $user, EventTemplate $eventTemplate): bool
    {
        return $this->view($user, $eventTemplate);
    }

    public function forceDelete(User $user, EventTemplate $eventTemplate): bool
    {
        return false;
    }
}
