<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use App\Services\GoogleCalendar\Data\GoogleEventData;
use Carbon\Carbon;

trait ToEvent
{
    public static function fromGoogleEvent(GoogleEventData $googleEventData): self
    {
        $startsAt = Carbon::parse($googleEventData->startDate);
        $endsAt = Carbon::parse($googleEventData->endDate);

        if (! is_null($googleEventData->startDateTime)) {
            $startsAt = Carbon::parse($googleEventData->startDateTime, $googleEventData->startTimezone)
                ->timezone('UTC');
        }

        if (! is_null($googleEventData->endDateTime)) {
            $endsAt = Carbon::parse($googleEventData->endDateTime, $googleEventData->endTimezone)
                ->timezone('UTC');
        }

        return new self(
            externalIdentifier: $googleEventData->id,
            title: $googleEventData->summary,
            allDay: is_null($googleEventData->startDateTime),
            startsAt: $startsAt,
            endsAt: $endsAt,
            description: $googleEventData->description,
        );
    }
}
