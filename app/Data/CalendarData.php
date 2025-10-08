<?php

namespace App\Data;

use App\Services\GoogleCalendar\Data\Normalizers\ToCalendar as FromGoogleCalendar;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CalendarData extends Data
{
    use FromGoogleCalendar;

    public function __construct(
        public string|Optional $externalId,
        public string|Optional $name,
        public string|Optional|null $description,
    ) {}
}
