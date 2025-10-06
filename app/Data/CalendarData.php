<?php

namespace App\Data;

use App\Services\GoogleCalendar\Data\Normalizers\ToCalendar as FromGoogleCalendar;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CalendarData extends Data
{
    use FromGoogleCalendar;

    public function __construct(
        public string|Optional $externalUserId,
        public string|Optional $externalToken,
        public string|Optional $externalRefreshToken,
        public Carbon|Optional $externalTokenExpiresAt,
        public string|Optional $externalCalendarId,
        public string|Optional $externalCalendarName,
        public string|Optional $externalCalendarDescription,
        public string|Optional $externalCalendarTimezone,
    ) {}
}
