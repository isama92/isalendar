<?php

namespace App\Services\GoogleCalendar\Data;

use Carbon\Carbon;
use Google\Service\Calendar\Event as GoogleEvent;
use Spatie\LaravelData\Data;

class GoogleEventData extends Data
{
    public function __construct(
        public string $id,
        public string $summary,
        public ?string $startDateTime,
        public ?string $startDate,
        public ?string $endDateTime,
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
            startDate: $startObj->getDate(),
            endDateTime: $endObj->getDateTime(),
            endDate: $endObj->getDate(),
            description: $googleEvent->getDescription(),
        );
    }

    public function startsAt(): Carbon
    {
        return Carbon::parse($this->startDateTime ?? $this->startDate);
    }

    public function endsAt(): Carbon
    {
        return Carbon::parse($this->endDateTime ?? $this->endDate);
    }

    public function allDay(): bool
    {
        return is_null($this->startDateTime);
    }
}
