<?php

namespace App\Services\GoogleCalendar\Actions\Events;

use App\Models\Calendar;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use Carbon\CarbonImmutable;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\Event as GoogleEvent;
use Illuminate\Support\Collection;

class FetchEvents
{
    public function __construct(private readonly GetAuthClient $getAuthClient) {}

    /**
     * @return \Illuminate\Support\Collection<int, \App\Services\GoogleCalendar\Data\GoogleEventData>
     */
    public function execute(
        Connector $connector,
        Calendar $calendar,
        CarbonImmutable $fetchWindowStart,
        CarbonImmutable $fetchWindowEnd,
    ): Collection {
        $googleClient = $this->getAuthClient->execute($connector);

        $service = new CalendarService($googleClient);

        $optParams = [
            'maxResults' => 2500,
            'singleEvents' => true,
            // Upper bound (exclusive) for an event's start time to filter by
            'timeMin' => $fetchWindowStart->toRfc3339String(),
            // Lower bound (exclusive) for an event's end time to filter by
            'timeMax' => $fetchWindowEnd->toRfc3339String(),
            'showDeleted' => true,
            'timeZone' => 'UTC',
        ];

        /** @var \Google\Service\Calendar\Events $eventList */
        $eventList = $service->events
            ->listEvents($calendar->external_id, $optParams);

        return collect($eventList->getItems())
            ->map(fn (GoogleEvent $googleEvent): GoogleEventData => GoogleEventData::from($googleEvent));
    }
}
