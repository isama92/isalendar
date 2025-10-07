<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use App\Services\GoogleCalendar\Data\GoogleEventData;
use Carbon\Carbon;

trait ToEvent
{
    public static function fromGoogleEvent(GoogleEventData $googleEventData): self
    {
        return new self(
            externalIdentifier: $googleEventData->id,
            title: $googleEventData->summary,
            allDay: is_null($googleEventData->startDateTime),
            startsAt: Carbon::parse($googleEventData->startDateTime ?? $googleEventData->startDate),
            endsAt: Carbon::parse($googleEventData->endDateTime ?? $googleEventData->endDate),
            description: $googleEventData->description,
        );
    }
}
