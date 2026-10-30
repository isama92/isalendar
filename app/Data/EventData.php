<?php

declare(strict_types=1);

namespace App\Data;

use App\Services\Calendar\GoogleCalendar\Data\Normalizers\ToEvent as FromGoogleCalendar;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class EventData extends Data
{
    use FromGoogleCalendar;

    public function __construct(
        public string|Optional|null $externalId,
        public string|Optional $title,
        public bool|Optional $allDay,
        public string|Optional|null $timezone,
        public Carbon|Optional $startsAt,
        public Carbon|Optional $endsAt,
        public string|Optional|null $description,
    ) {}
}
