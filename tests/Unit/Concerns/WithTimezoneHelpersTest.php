<?php

use App\Concerns\WithTimezoneHelpers;
use Illuminate\Support\Collection;

uses(WithTimezoneHelpers::class)
    ->group('concerns');

it('returns a collection of timezones', function (): void {
    $result = $this->timezones();

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->not->toBeEmpty()
        ->first()->toBeString()
        ->contains('Europe/Amsterdam')->toBeTrue()
        ->contains('America/New_York')->toBeTrue()
        ->contains('UTC')->toBeTrue();
});

it('returns grouped timezones', function (): void {
    $result = $this->groupedTimezones();
    $europeGroup = $result->firstWhere('label', 'Europe');
    $amsterdamOption = collect($europeGroup['options'])
        ->firstWhere('value', 'Europe/Amsterdam');

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->not->toBeEmpty()
        ->and($europeGroup)
        ->toBeArray()
        ->toHaveKeys(['label', 'options'])
        ->and(array_keys($europeGroup['options']))->toBe(range(0, count($europeGroup['options']) - 1))
        ->and($europeGroup['label'])->toBe('Europe')
        ->and($europeGroup['options'])->toBeArray()
        ->and($amsterdamOption)->toBeArray()
        ->toHaveKeys(['value', 'label'])
        ->and($amsterdamOption['value'])->toBe('Europe/Amsterdam')
        ->and($amsterdamOption['label'])->toBe('Europe/Amsterdam');
});

it('sorts groups and timezones alphabetically by label', function (): void {
    $result = $this->groupedTimezones();
    $europeGroup = $result->firstWhere('label', 'Europe');
    $timezones = collect($europeGroup['options'])->pluck('value');

    $labels = $result->pluck('label');

    expect($labels->toArray())
        ->toBe($labels->sort()->values()->toArray())
        ->and($timezones->toArray())
        ->toBe($timezones->sort()->values()->toArray());
});
