<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use Carbon\Carbon;
use Google\Service\Calendar\Event as GoogleEvent;

trait ToEvent
{
    public static function fromGoogleEvent(GoogleEvent $googleEvent): self
    {
        $startObj = $googleEvent->getStart();
        $endObj = $googleEvent->getEnd();

        /** @var null|string $startDateTime */
        $startDateTime = $startObj->getDateTime();

        /** @var null|string $endDateTime */
        $endDateTime = $endObj->getDateTime();

        return new self(
            externalIdentifier: $googleEvent->id,
            title: $googleEvent->getSummary(),
            allDay: is_null($startDateTime),
            startsAt: Carbon::parse($startDateTime ?? $startObj->getDate()),
            endsAt: Carbon::parse($endDateTime ?? $endObj->getDate()),
            description: $googleEvent->getDescription(),
        );
    }
}
