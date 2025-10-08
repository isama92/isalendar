<?php

namespace App\Enums;

use App\Exceptions\UnexpectedProvider;
use App\Services\GoogleCalendar\Auth\AccessToken as GoogleCalendarAccessToken;

enum Service: string
{
    case GOOGLE_CALENDAR = 'Google Calendar';

    public function id(): int
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 1,
        };
    }

    public function enabled(): bool
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => true,
        };
    }

    /**
     * @return class-string
     */
    public function accessObject(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => GoogleCalendarAccessToken::class,
        };
    }

    public function authRoute(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'services.google.auth.connect',
        };
    }

    public static function fromId(?int $id): self
    {
        return match ($id) {
            self::GOOGLE_CALENDAR->id() => self::GOOGLE_CALENDAR,
            default => throw new UnexpectedProvider($id),
        };
    }
}
