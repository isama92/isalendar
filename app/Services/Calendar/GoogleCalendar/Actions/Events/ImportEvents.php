<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Models\Calendar;
use App\Models\Connector;
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
        Connector $connector,
        Calendar $calendar,
        CarbonImmutable $fetchWindowStart,
        CarbonImmutable $fetchWindowEnd,
    ): void {
        $this->fetchEvents->execute($connector, $calendar, $fetchWindowStart, $fetchWindowEnd)
            ->each(fn (GoogleEventData $googleEventData) => $this->saveEvent
                ->execute($googleEventData, $calendar));
    }
}
