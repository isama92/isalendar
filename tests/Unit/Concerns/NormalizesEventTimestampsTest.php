<?php

use App\Concerns\NormalizesEventTimestamps;

uses(NormalizesEventTimestamps::class)
    ->group('concerns');

it('returns null when value is null', function (): void {
    $result = test()->normalizeEventTimestamp(null, false, 'Europe/Amsterdam');

    expect($result)->toBeNull();
});

it('returns null when value is empty string', function (): void {
    $result = test()->normalizeEventTimestamp('', false, 'Europe/Amsterdam');

    expect($result)->toBeNull();
});

it('normalizes non-all-day event to UTC', function (): void {
    $result = test()->normalizeEventTimestamp('2025-01-15 14:30:00', false, 'Europe/Amsterdam');

    expect($result)
        ->toBeString()
        ->toBe('2025-01-15 13:30:00');
});

it('normalizes all-day event to start of day in UTC', function (): void {
    $result = test()->normalizeEventTimestamp('2025-01-15', true, 'Europe/Amsterdam');

    expect($result)
        ->toBeString()
        ->toBe('2025-01-15 00:00:00');
});

it('handles ISO 8601 format', function (): void {
    $result = test()->normalizeEventTimestamp('2025-06-15T18:30:00+02:00', false, 'Europe/Amsterdam');

    expect($result)
        ->toBeString()
        ->toBe('2025-06-15T16:30:00');
});
