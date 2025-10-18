<?php

namespace App\Services\Calendar\GoogleCalendar\Data;

use Google\Service\Calendar\CalendarListEntry as GoogleCalendar;
use Spatie\LaravelData\Data;

class GoogleCalendarData extends Data
{
    public function __construct(
        public string $id,
        public string $summary,
        public ?string $description,
    ) {}

    public static function fromGoogleCalendar(GoogleCalendar $googleCalendar): self
    {
        return new self(
            id: $googleCalendar->getId(),
            summary: $googleCalendar->getSummary(),
            description: $googleCalendar->getDescription(),
        );
    }
}
