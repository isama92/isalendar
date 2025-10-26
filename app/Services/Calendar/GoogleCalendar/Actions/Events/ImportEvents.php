<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Models\Calendar;
use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use Carbon\CarbonImmutable;
use Spatie\QueueableAction\QueueableAction;

class ImportEvents
{
    use QueueableAction;

    public function __construct(
        private readonly FetchEvents $fetchEvents,
        private readonly SaveEvent $saveEvent,
    ) {}

    public function execute(
        Calendar $calendar,
        CarbonImmutable $fetchWindowStart,
        CarbonImmutable $fetchWindowEnd,
    ): void {
        $connector = $calendar->connectors()->firstOrFail();

        $this->fetchEvents->execute($connector, $calendar, $fetchWindowStart, $fetchWindowEnd)
            ->each(fn (GoogleEventData $googleEventData) => $this->saveEvent
                ->execute($connector, $calendar, $googleEventData));
    }
}
