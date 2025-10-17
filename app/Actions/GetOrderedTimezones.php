<?php

namespace App\Actions;

use Illuminate\Support\Collection;

class GetOrderedTimezones
{
    /**
     * @return \Illuminate\Support\Collection<int, array{value: string, label: string}>
     */
    public function execute(): Collection
    {
        $now = now()->toImmutable();

        return collect(timezone_identifiers_list())
            ->map(function (string $tz) use ($now): array {
                $datetime = $now->copy()->setTimezone($tz);
                $offset = $datetime->format('P');
                $offsetSeconds = $datetime->getOffset();

                return [
                    'timezone' => $tz,
                    'offset' => $offset,
                    'offsetSeconds' => $offsetSeconds,
                ];
            })
            ->sortBy([
                ['offsetSeconds', 'asc'],
                ['timezone', 'asc'],
            ])
            ->map(fn (array $data): array => [
                'value' => $data['timezone'],
                'label' => "(UTC{$data['offset']}) {$data['timezone']}",
            ])
            ->values();
    }
}
