<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use App\Services\GoogleCalendar\Data\GoogleEventData;

trait ToEvent
{
    public static function fromGoogleEvent(GoogleEventData $googleEventData): self
    {
        return new self(
            externalIdentifier: $googleEventData->id,
            title: $googleEventData->summary,
            allDay: $googleEventData->allDay(),
            startsAt: $googleEventData->startsAt(),
            endsAt: $googleEventData->endsAt(),
            description: $googleEventData->description,
        );
    }
}
