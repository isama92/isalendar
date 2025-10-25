<?php

namespace App\Services\Calendar\GoogleCalendar\Data\Normalizers;

use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use Carbon\Carbon;

trait ToEvent
{
    public static function fromGoogleEvent(GoogleEventData $googleEventData): self
    {
        return new self(
            externalId: $googleEventData->id,
            title: $googleEventData->summary ?? '',
            allDay: is_null($googleEventData->startDateTime),
            timezone: $googleEventData->timezone,
            startsAt: Carbon::parse($googleEventData->startDateTime ?? $googleEventData->startDate),
            endsAt: Carbon::parse($googleEventData->endDateTime ?? $googleEventData->endDate),
            description: $googleEventData->sanitizedDescription(),
        );
    }
}
