<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Models\Calendar;
use App\Services\GoogleCalendar\Actions\Events\FetchEvents;
use App\Services\GoogleCalendar\Actions\Events\SaveEvent;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use Spatie\QueueableAction\QueueableAction;

class SynchronizeCalendarEvents
{
    use QueueableAction;

    public function __construct(
        private readonly FetchEvents $fetchEvents,
        private readonly SaveEvent $saveEvent,
    ) {}

    public function execute(Calendar $calendar): void
    {
        $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
        $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

        $this->fetchEvents->execute($calendar, $fetchWindowStart, $fetchWindowEnd)
            ->each(fn (GoogleEventData $googleEventData) => $this->saveEvent
                ->execute($googleEventData, $calendar));
    }
}
