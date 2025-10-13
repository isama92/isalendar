<?php

namespace App\Services\GoogleCalendar\Data;

use App\Services\GoogleCalendar\Enums\Status;
use Carbon\Carbon;
use Google\Service\Calendar\Event as GoogleEvent;
use Spatie\LaravelData\Data;

/**
 * @SuppressWarnings("PHPMD.ExcessiveParameterList")
 */
class GoogleEventData extends Data
{
    public function __construct(
        public string $id,
        public Status $status,
        public Carbon $updatedAt,
        public ?string $summary,
        public ?string $timezone,
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
            status: Status::from($googleEvent->getStatus()),
            updatedAt: Carbon::parse($googleEvent->getUpdated()),
            summary: $googleEvent->getSummary(),
            timezone: $startObj->getTimezone(),
            startDateTime: $startObj->getDateTime(),
            startDate: $startObj->getDate(),
            endDateTime: $endObj->getDateTime(),
            endDate: $endObj->getDate(),
            description: $googleEvent->getDescription(),
        );
    }
}
