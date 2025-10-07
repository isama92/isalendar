<?php

namespace App\Services\GoogleCalendar\Data;

use Google\Service\Calendar\Event as GoogleEvent;
use Spatie\LaravelData\Data;

class GoogleEventData extends Data
{
    public function __construct(
        public string $id,
        public string $summary,
        public ?string $startDateTime,
        public ?string $startTimezone,
        public ?string $startDate,
        public ?string $endDateTime,
        public ?string $endTimezone,
        public ?string $endDate,
        public ?string $description,
    ) {}

    public static function fromGoogleEvent(GoogleEvent $googleEvent): self
    {
        $startObj = $googleEvent->getStart();
        $endObj = $googleEvent->getEnd();

        return new self(
            id: $googleEvent->getId(),
            summary: $googleEvent->getSummary(),
            startDateTime: $startObj->getDateTime(),
            startTimezone: $startObj->getTimezone(),
            startDate: $startObj->getDate(),
            endDateTime: $endObj->getDateTime(),
            endTimezone: $endObj->getTimezone(),
            endDate: $endObj->getDate(),
            description: $googleEvent->getDescription(),
        );
    }
}
