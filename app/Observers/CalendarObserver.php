<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Calendar;

class CalendarObserver
{
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
