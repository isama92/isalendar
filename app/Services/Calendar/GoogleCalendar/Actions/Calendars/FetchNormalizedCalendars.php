<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Calendars;

use App\Data\CalendarData;
use App\Models\Connector;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\Calendar\GoogleCalendar\Data\GoogleCalendarData;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\CalendarList;
use Google\Service\Calendar\CalendarListEntry as GoogleCalendar;
use Illuminate\Support\Collection;

class FetchNormalizedCalendars
{
    public function __construct(private readonly GetAuthClient $getAuthClient) {}

    /**
     * @return Collection<int, CalendarData>
     */
    public function execute(Connector $connector): Collection
    {
        $googleClient = $this->getAuthClient->execute($connector);
        $service = new CalendarService($googleClient);

        /** @var CalendarList $calendarList */
        $calendarList = $service->calendarList
            ->listCalendarList();

        return collect($calendarList->getItems())
            ->map(fn (GoogleCalendar $googleCalendar): GoogleCalendarData => GoogleCalendarData::from($googleCalendar))
            ->map(fn (GoogleCalendarData $googleCalendarData): CalendarData => CalendarData::from($googleCalendarData));
    }
}
