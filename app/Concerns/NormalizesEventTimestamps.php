<?php

namespace App\Concerns;

use Carbon\Carbon;

trait NormalizesEventTimestamps
{
    public function normalizeEventTimestamp(?string $value, string $tz, bool $allDay): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($allDay) {
            return Carbon::parse($value, 'UTC')->startOfDay();
        }

        return Carbon::parse($value, $tz)
            ->setTimezone('UTC')
            ->toDateTimeString();
    }
}
