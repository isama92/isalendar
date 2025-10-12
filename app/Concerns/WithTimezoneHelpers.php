<?php

namespace App\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait WithTimezoneHelpers
{
    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function timezones(): Collection
    {
        return collect(timezone_identifiers_list());
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{label: string, options: array<int, array{value: string, label: string}>}>
     */
    public function groupedTimezones(): Collection
    {
        return $this->timezones()
            ->groupBy(fn ($tz) => Str::before($tz, '/'))
            ->map(fn (Collection $zones, string $label): array => [
                'label' => $label,
                'options' => $zones
                    ->sort()
                    ->values()
                    ->map(fn (string $tz): array => ['value' => $tz, 'label' => $tz])
                    ->all(),
            ])
            ->sortBy('label')
            ->values();
    }
}
