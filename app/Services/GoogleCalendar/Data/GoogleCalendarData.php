<?php

namespace App\Services\GoogleCalendar\Data;

use Google\Service\Calendar\CalendarListEntry as GoogleCalendar;
use Spatie\LaravelData\Data;

class GoogleCalendarData extends Data
{
    public function __construct(
        public string $id,
        public string $summary,
        public string $description,
        public string $timezone,
    ) {}

    public static function fromGoogleCalendar(GoogleCalendar $googleCalendar): self
    {
        return new self(
            id: $googleCalendar->getId(),
            summary: $googleCalendar->getSummary(),
            description: $googleCalendar->getDescription(),
            timezone: $googleCalendar->getTimeZone(),
        );
    }
}
