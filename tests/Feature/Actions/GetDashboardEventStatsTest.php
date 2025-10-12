<?php

use App\Actions\GetDashboardEventStats;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;

uses()->group('actions');

it('returns correct data structure with all required keys', function (): void {
    $user = User::factory()->create();
    actingAs($user);

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
    $user = User::factory()->create();
    actingAs($user);

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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    Event::factory()->count(5)->for($calendar)->create();

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalEvents'])->toBe(5);
});

it('counts total calendars correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();
    $calendar3 = Calendar::factory()->create();

    $calendar1->connectors()->attach($connector);
    $calendar2->connectors()->attach($connector);
    $calendar3->connectors()->attach($connector);

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalCalendars'])->toBe(3);
});

it('counts events for today correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();

    Event::factory()->for($calendar)->create(['starts_at' => $today->copy()->addHours(10)]);
    Event::factory()->for($calendar)->create(['starts_at' => $today->copy()->addHours(14)]);
    Event::factory()->for($calendar)->create(['starts_at' => $today->copy()->addDay()]); // Tomorrow, should not count

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsToday'])->toBe(2);
});

it('counts all-day events for today correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();

    Event::factory()->for($calendar)->create([
        'all_day' => true,
        'starts_at' => $today,
        'ends_at' => null,
    ]);
    Event::factory()->for($calendar)->create([
        'all_day' => false,
        'starts_at' => $today->copy()->addHours(10),
    ]);

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsToday'])->toBe(2);
});

it('counts events for tomorrow correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();
    $tomorrow = $today->copy()->addDay();

    Event::factory()->for($calendar)->create(['starts_at' => $today->copy()->addHours(10)]); // Today
    Event::factory()->for($calendar)->create(['starts_at' => $tomorrow->copy()->addHours(10)]); // Tomorrow
    Event::factory()->for($calendar)->create(['starts_at' => $tomorrow->copy()->addHours(14)]); // Tomorrow

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsTomorrow'])->toBe(2);
});

it('counts events for this week correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();
    $thisWeekStart = $today->copy()->startOfWeek();
    $thisWeekEnd = $today->copy()->endOfWeek();
    $nextWeek = $today->copy()->addWeek();

    Event::factory()->for($calendar)->create(['starts_at' => $thisWeekStart->copy()->addDay()]);
    Event::factory()->for($calendar)->create(['starts_at' => $thisWeekStart->copy()->addDays(3)]);
    Event::factory()->for($calendar)->create(['starts_at' => $nextWeek]); // Next week

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsThisWeek'])->toBeGreaterThanOrEqual(2);
});

it('counts events for next week correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();
    $nextWeekStart = $today->copy()->addWeek()->startOfWeek();

    Event::factory()->for($calendar)->create(['starts_at' => $today]); // This week
    Event::factory()->for($calendar)->create(['starts_at' => $nextWeekStart->copy()->addDay()]);
    Event::factory()->for($calendar)->create(['starts_at' => $nextWeekStart->copy()->addDays(3)]);

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsNextWeek'])->toBeGreaterThanOrEqual(2);
});

it('calculates average events per day of week correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();
    $sunday = $today->copy()->startOfWeek()->subDay();
    $monday = $today->copy()->startOfWeek();
    $wednesday = $today->copy()->startOfWeek()->addDays(2);

    // 3 events on Sunday
    Event::factory()->count(3)->for($calendar)->create(['starts_at' => $sunday->copy()->addHours(10)]);
    // 5 events on Monday
    Event::factory()->count(5)->for($calendar)->create(['starts_at' => $monday->copy()->addHours(10)]);
    // 2 events on Wednesday
    Event::factory()->count(2)->for($calendar)->create(['starts_at' => $wednesday->copy()->addHours(10)]);

    actingAs($user);

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
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = Connector::factory()->for($user1)->create();
    $connector2 = Connector::factory()->for($user2)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();

    $calendar1->connectors()->attach($connector1);
    $calendar2->connectors()->attach($connector2);

    Event::factory()->count(5)->for($calendar1)->create();
    Event::factory()->count(3)->for($calendar2)->create();

    actingAs($user1);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalEvents'])->toBe(5)
        ->and($stats['totalCalendars'])->toBe(1);
});

it('only counts calendars for the authenticated user', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = Connector::factory()->for($user1)->create();
    $connector2 = Connector::factory()->for($user2)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();
    $calendar3 = Calendar::factory()->create();

    $calendar1->connectors()->attach($connector1);
    $calendar2->connectors()->attach($connector1);
    $calendar3->connectors()->attach($connector2);

    actingAs($user1);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['totalCalendars'])->toBe(2);
});

it('handles mixed regular and all-day events in counts', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();

    Event::factory()->for($calendar)->create([
        'all_day' => true,
        'starts_at' => $today,
        'ends_at' => null,
    ]);
    Event::factory()->for($calendar)->create([
        'all_day' => false,
        'starts_at' => $today->copy()->addHours(10),
    ]);
    Event::factory()->for($calendar)->create([
        'all_day' => false,
        'starts_at' => $today->copy()->addHours(15),
    ]);

    actingAs($user);

    $stats = app(GetDashboardEventStats::class)->execute();

    expect($stats['eventsToday'])->toBe(3)
        ->and($stats['totalEvents'])->toBe(3);
});
