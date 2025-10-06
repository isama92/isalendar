<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use App\Services\GoogleCalendar\Data\GoogleCalendarData;
use App\Services\GoogleCalendar\Data\GoogleUserData;
use Spatie\LaravelData\Optional;

trait ToCalendar
{
    public static function fromGoogleUser(GoogleUserData $googleUserData): self
    {
        return new self(
            externalUserId: $googleUserData->id,
            externalToken: $googleUserData->token,
            externalRefreshToken: $googleUserData->refreshToken,
            externalTokenExpiresAt: $googleUserData->expiresAt(),
            externalCalendarId: Optional::create(),
            externalCalendarName: Optional::create(),
            externalCalendarDescription: Optional::create(),
            externalCalendarTimezone: Optional::create(),
        );
    }

    public static function fromGoogleCalendar(GoogleCalendarData $googleCalendarData): self
    {
        return new self(
            externalUserId: Optional::create(),
            externalToken: Optional::create(),
            externalRefreshToken: Optional::create(),
            externalTokenExpiresAt: Optional::create(),
            externalCalendarId: $googleCalendarData->id,
            externalCalendarName: $googleCalendarData->summary,
            externalCalendarDescription: $googleCalendarData->description,
            externalCalendarTimezone: $googleCalendarData->timezone,
        );
    }
}
