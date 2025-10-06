<?php

namespace App\Services\GoogleCalendar\Data\Normalizers;

use Laravel\Socialite\Two\User as GoogleUser;

trait ToCalendar
{
    public static function fromGoogleUser(GoogleUser $googleUser): self
    {
        return new self(
            externalUserId: $googleUser->id,
            externalCalendarId: 'primary', // TODO: this should be the calendar the user decides
            externalToken: $googleUser->token,
            externalRefreshToken: $googleUser->refreshToken,
            externalTokenExpiresAt: now()->addSeconds($googleUser->expiresIn),
        );
    }
}
