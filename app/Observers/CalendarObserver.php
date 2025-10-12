<?php

namespace App\Observers;

use App\Models\Calendar;

class CalendarObserver
{
    public function created(Calendar $calendar): void
    {
        // TODO: create command to only sync one calendar?
    }

    public function deleted(Calendar $calendar): void
    {
        $calendar->events()
            ->lazyById()
            ->each
            ->delete();
    }

    public function restored(Calendar $calendar): void
    {
        $calendar->update(['enabled' => true]);
    }
}
