<?php

use App\Actions\GetOrderedTimezones;
use Illuminate\Support\Collection;

uses()->group('actions');

it('returns flat list of timezones with offsets in labels', function (): void {
    $result = app(GetOrderedTimezones::class)->execute();
    $amsterdamOption = $result->firstWhere('value', 'Europe/Amsterdam');
    $timezone = now()->setTimezone('Europe/Amsterdam')->getTimezone();
    $region = $timezone->toRegionName();
    $offset = $timezone->toOffsetName();

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->not->toBeEmpty()
        ->first()->toBeArray()
        ->toHaveKeys(['value', 'label'])
        ->and($amsterdamOption)->toBeArray()
        ->toHaveKeys(['value', 'label'])
        ->and($amsterdamOption['value'])->toBe($region)
        ->and($amsterdamOption['label'])->toBe(sprintf('(%s) %s', $offset, $region));
});

it('sorts timezones globally by offset', function (): void {
    $result = app(GetOrderedTimezones::class)->execute();

    expect($result->first()['label'])->toBe('(-11:00) Pacific/Midway')
        ->and($result->last()['label'])->toBe('(+14:00) Pacific/Kiritimati');
});
