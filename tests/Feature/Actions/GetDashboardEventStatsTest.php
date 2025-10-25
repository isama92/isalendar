<?php

use App\Actions\GetDashboardEventStats;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('actions');

it('returns correct data structure with all required keys', function (): void {
    actingAsUser();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats)
        ->toBeArray()
        ->toHaveKeys([
            'totalEvents',
            'totalCalendars',
            'eventsToday',
            'eventsTomorrow',
            'eventsThisWeek',
            'eventsNextWeek',
            'averagePerDayOfWeek',
        ])
        ->and($stats['averagePerDayOfWeek'])->toBeArray()->toHaveCount(7);
});

it('returns zero counts when user has no events', function (): void {
    actingAsUser();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalEvents'])->toBe(0)
        ->and($stats['totalCalendars'])->toBe(0)
        ->and($stats['eventsToday'])->toBe(0)
        ->and($stats['eventsTomorrow'])->toBe(0)
        ->and($stats['eventsThisWeek'])->toBe(0)
        ->and($stats['eventsNextWeek'])->toBe(0)
        ->and($stats['averagePerDayOfWeek'])->toBe([0, 0, 0, 0, 0, 0, 0]);
});

it('counts total events correctly', function (): void {
    actingAsUser();
    eventFactory(5)->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalEvents'])->toBe(5);
});

it('counts total calendars correctly', function (): void {
    actingAsUser();

    // creates 1 connector with 3 calendars
    calendarFactory(3)->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalCalendars'])->toBe(3);
});

it('counts events for today correctly', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();

    actingAsUser();
    eventFactory(3)
        ->state(new Sequence(
            ['all_day' => false, 'starts_at' => $today->addHours(10), 'ends_at' => $today->addHours(11)],
            ['all_day' => false, 'starts_at' => $today->addHours(14), 'ends_at' => $today->addHours(15)],
            ['all_day' => false, 'starts_at' => $today->addDay(), 'ends_at' => $today->addDay()->addHours(1)],
        ))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsToday'])->toBe(2);
});

it('counts all-day events for today correctly', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();

    actingAsUser();
    eventFactory(2)
        ->state(new Sequence(
            ['all_day' => true, 'starts_at' => $today, 'ends_at' => $today->addDay()],
            ['all_day' => false, 'starts_at' => $today->addHours(10), 'ends_at' => $today->addHours(11)],
        ))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsToday'])->toBe(2);
});

it('counts events for tomorrow correctly', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();
    $tomorrow = $today->addDay();

    actingAsUser();
    eventFactory(3)
        ->state(new Sequence(
            ['all_day' => false, 'starts_at' => $today->addHours(10), 'ends_at' => $today->addHours(11)], // Today
            ['all_day' => false, 'starts_at' => $tomorrow->addHours(10), 'ends_at' => $tomorrow->addHours(11)], // Tomorrow
            ['all_day' => false, 'starts_at' => $tomorrow->addHours(14), 'ends_at' => $tomorrow->addHours(15)], // Tomorrow
        ))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsTomorrow'])->toBe(2);
});

it('counts events for this week correctly', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();
    $thisWeekStart = $today->startOfWeek();
    $nextWeek = $today->addWeek();

    actingAsUser();
    eventFactory(3)
        ->state(new Sequence(
            ['all_day' => false, 'starts_at' => $thisWeekStart->addDay(), 'ends_at' => $thisWeekStart->addDay()->addHours(1)],
            ['all_day' => false, 'starts_at' => $thisWeekStart->addDays(3), 'ends_at' => $thisWeekStart->addDays(3)->addHours(1)],
            ['all_day' => false, 'starts_at' => $nextWeek, 'ends_at' => $nextWeek->addHours(1)], // Next week
        ))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsThisWeek'])->toBeGreaterThanOrEqual(2);
});

it('counts events for next week correctly', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();
    $nextWeekStart = $today->addWeek()->startOfWeek();

    actingAsUser();
    eventFactory(3)
        ->state(new Sequence(
            ['all_day' => false, 'starts_at' => $today, 'ends_at' => $today->addHours(1)], // This week
            ['all_day' => false, 'starts_at' => $nextWeekStart->addDay(), 'ends_at' => $nextWeekStart->addDay()->addHours(1)],
            ['all_day' => false, 'starts_at' => $nextWeekStart->addDays(3), 'ends_at' => $nextWeekStart->addDays(3)->addHours(1)],
        ))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsNextWeek'])->toBeGreaterThanOrEqual(2);
});

it('calculates average events per day of week correctly', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();
    $sunday = $today->startOfWeek()->subDay();
    $monday = $today->startOfWeek();
    $wednesday = $today->startOfWeek()->addDays(2);

    actingAsUser();

    // 3 events on Sunday
    eventFactory(3)->create(['all_day' => false, 'starts_at' => $sunday->addHours(10), 'ends_at' => $sunday->addHours(11)]);
    // 5 events on Monday
    eventFactory(5)->create(['all_day' => false, 'starts_at' => $monday->addHours(10), 'ends_at' => $monday->addHours(11)]);
    // 2 events on Wednesday
    eventFactory(2)->create(['all_day' => false, 'starts_at' => $wednesday->addHours(10), 'ends_at' => $wednesday->addHours(11)]);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['averagePerDayOfWeek'])
        ->toBeArray()
        ->toHaveCount(7)
        ->sequence(
            fn ($day) => $day->toBe(3), // Sunday
            fn ($day) => $day->toBe(5), // Monday
            fn ($day) => $day->toBe(0), // Tuesday
            fn ($day) => $day->toBe(2), // Wednesday
            fn ($day) => $day->toBe(0), // Thursday
            fn ($day) => $day->toBe(0), // Friday
            fn ($day) => $day->toBe(0)  // Saturday
        );
});

it('only counts events for the authenticated user', function (): void {
    actingAsUser();
    eventFactory(5)->create();

    // Create events for a different user
    $connector = connectorFactory()->for(User::factory())->create();
    Event::factory()->count(3)
        ->for(calendarFactory(connector: $connector))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalEvents'])->toBe(5)
        ->and($stats['totalCalendars'])->toBe(1);
});

it('only counts calendars for the authenticated user', function (): void {
    actingAsUser();

    // creates 1 connector with 2 calendars for authenticated user
    calendarFactory(2)->create();

    // Create calendar for a different user
    $connector = connectorFactory()->for(User::factory())->create();
    calendarFactory(connector: $connector)->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalCalendars'])->toBe(2);
});

it('handles mixed regular and all-day events in counts', function (): void {
    $today = Carbon::now()->startOfDay()->toImmutable();

    actingAsUser();
    eventFactory(3)
        ->state(new Sequence(
            ['all_day' => true, 'starts_at' => $today, 'ends_at' => $today->addDay()],
            ['all_day' => false, 'starts_at' => $today->addHours(10), 'ends_at' => $today->addHours(11)],
            ['all_day' => false, 'starts_at' => $today->addHours(15), 'ends_at' => $today->addHours(16)],
        ))
        ->create();

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsToday'])->toBe(3)
        ->and($stats['totalEvents'])->toBe(3);
});
