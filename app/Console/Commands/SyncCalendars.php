<?php

namespace App\Console\Commands;

use App\Models\Calendar;
use App\Services\GoogleCalendar\Actions\SynchronizeCalendarEvents;
use Illuminate\Console\Command;

class SyncCalendars extends Command
{
    protected $signature = 'app:sync:calendars';

    protected $description = 'Synchronize calendars events';

    public function handle(SynchronizeCalendarEvents $synchronizeCalendarEvents): int
    {
        Calendar::query()
            ->lazyById()
            ->each(fn (Calendar $calendar) => $synchronizeCalendarEvents
                ->onQueue()
                ->execute($calendar));

        return self::SUCCESS;
    }
}
