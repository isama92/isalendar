<?php

namespace App\Observers;

use App\Models\Event;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class EventObserver implements ShouldHandleEventsAfterCommit
{
    // TODO: remember the Event model tests of these methods
    public function created(Event $event): void
    {
        // TODO: push event remotely
    }

    public function updated(Event $event): void
    {
        // TODO: push event remotely
    }

    public function deleted(Event $event): void
    {
        // TODO: delete event remotely
    }

    public function restored(Event $event): void
    {
        // TODO: push event remotely
    }
}
