<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use App\Services\GoogleCalendar\Data\GoogleCalendarData;

trait ToCalendar
{
    public static function fromGoogleCalendar(GoogleCalendarData $googleCalendarData): self
    {
        return new self(
            externalId: $googleCalendarData->id,
            name: $googleCalendarData->summary,
            description: $googleCalendarData->description,
        );
    }
}
