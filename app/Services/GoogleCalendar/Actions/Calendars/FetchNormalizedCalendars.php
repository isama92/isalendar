<?php

namespace App\Services\GoogleCalendar\Actions\Calendars;

use App\Data\CalendarData;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\GoogleCalendar\Data\GoogleCalendarData;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\CalendarListEntry as GoogleCalendar;
use Illuminate\Support\Collection;

class FetchNormalizedCalendars
{
    public function __construct(private readonly GetAuthClient $getAuthClient) {}

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\CalendarData>
     */
    public function execute(Connector $connector): Collection
    {
        $googleClient = $this->getAuthClient->execute($connector);
        $service = new CalendarService($googleClient);

        /** @var \Google\Service\Calendar\CalendarList $calendarList */
        $calendarList = $service->calendarList
            ->listCalendarList();

        return collect($calendarList->getItems())
            ->map(fn (GoogleCalendar $googleCalendar): GoogleCalendarData => GoogleCalendarData::from($googleCalendar))
            ->map(fn (GoogleCalendarData $googleCalendarData): CalendarData => CalendarData::from($googleCalendarData));
    }
}
