<?php

namespace App\Actions;

use App\Enums\Service;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Calendars\FetchNormalizedCalendars as GoogleCalendarFetchNormalizedCalendars;
use Illuminate\Support\Collection;

class GetProviderCalendars
{
    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\CalendarData>
     */
    public function execute(Connector $connector): Collection
    {
        $fetchNormalizeCalendars = match (Service::fromId($connector->provider_id)) {
            Service::GOOGLE_CALENDAR => GoogleCalendarFetchNormalizedCalendars::class,
            Service::MICROSOFT_OUTLOOK => null,
        };

        if (is_null($fetchNormalizeCalendars)) {
            return collect();
        }

        return app($fetchNormalizeCalendars)->execute($connector);
    }
}
