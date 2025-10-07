<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Models\Calendar;
use App\Services\GoogleCalendar\Data\GoogleCalendarData;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\CalendarListEntry as GoogleCalendar;
use Illuminate\Support\Collection;

readonly class GetCalendars
{
    public function __construct(private GetAuthClient $getAuthClient) {}

    /**
     * @return \Illuminate\Support\Collection<int, \App\Services\GoogleCalendar\Data\GoogleCalendarData>
     */
    public function execute(Calendar $calendar): Collection
    {
        $googleClient = $this->getAuthClient->execute($calendar);
        $service = new CalendarService($googleClient);

        /** @var \Google\Service\Calendar\CalendarList $calendarList */
        $calendarList = $service->calendarList
            ->listCalendarList();

        return collect($calendarList->getItems())
            ->map(fn (GoogleCalendar $googleCalendar): GoogleCalendarData => GoogleCalendarData::from($googleCalendar));
    }
}
