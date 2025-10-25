<?php

namespace App\Enums;

use App\Exceptions\UnexpectedProviderException;
use App\Services\Calendar\GoogleCalendar\Auth\AccessToken as GoogleCalendarAccessToken;

enum Service: string
{
    case GOOGLE_CALENDAR = 'Google Calendar';
    case MICROSOFT_OUTLOOK = 'Microsoft Outlook';

    public function id(): int
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 1,
            self::MICROSOFT_OUTLOOK => 2,
        };
    }

    public function imagePath(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'google.webp',
            self::MICROSOFT_OUTLOOK => 'microsoft.webp',
        };
    }

    public function enabled(): bool
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => true,
            self::MICROSOFT_OUTLOOK => false,
        };
    }

    /**
     * @return class-string
     */
    public function accessObject(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => GoogleCalendarAccessToken::class,
            self::MICROSOFT_OUTLOOK => '',
        };
    }

    public function authRoute(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'services.google.auth.connect',
            self::MICROSOFT_OUTLOOK => 'services.microsoft.auth.connect',
        };
    }

    /**
     * @return array<int, string>
     */
    public function scopes(): array
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => [
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/calendar.events',
                'openid',
                'profile',
                'email',
            ],
            self::MICROSOFT_OUTLOOK => [],
        };
    }

    /**
     * @return array<int, string>
     */
    public function accessParams(): array
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => [
                'access_type' => 'offline',
                'prompt' => 'consent',
            ],
            self::MICROSOFT_OUTLOOK => [],
        };
    }

    public static function fromId(?int $id): self
    {
        return match ($id) {
            self::GOOGLE_CALENDAR->id() => self::GOOGLE_CALENDAR,
            self::MICROSOFT_OUTLOOK->id() => self::MICROSOFT_OUTLOOK,
            default => throw new UnexpectedProviderException($id),
        };
    }
}
