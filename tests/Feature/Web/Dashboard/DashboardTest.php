<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'dashboard');

it('displays stats for authenticated user', function (): void {
    actingAsUser();

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('totalEvents')
            ->has('totalCalendars')
            ->has('eventsToday')
            ->has('eventsTomorrow')
            ->has('eventsThisWeek')
            ->has('eventsNextWeek')
            ->has('averagePerDayOfWeek')
            ->where('averagePerDayOfWeek', fn ($value): bool => count($value) === 7)
        );
});

it('redirects unauthenticated users to login', function (): void {
    get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('shows correct event counts', function (): void {
    Carbon::setTestNow('2025-01-06 00:00:00');

    actingAsUser();

    $today = now()->startOfDay()->toImmutable();
    $tomorrow = $today->addDay();
    $thisWeekStart = $today->startOfWeek();
    $nextWeekStart = $thisWeekStart->addWeek();

    // Create events for different time periods
    eventFactory(4)
        ->state(new Sequence(
            ['starts_at' => $today->addHours(10)],
            ['starts_at' => $tomorrow->addHours(10)],
            ['starts_at' => $thisWeekStart->addDays(3)],
            ['starts_at' => $nextWeekStart->addDays(2)],
        ))
        ->create();

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('totalEvents', 4)
            ->where('totalCalendars', 1)
            ->where('eventsToday', 1)
            ->where('eventsTomorrow', 1)
            ->where('eventsThisWeek', fn ($value): bool => $value >= 1)
            ->where('eventsNextWeek', fn ($value): bool => $value >= 1)
        );
});

it('shows correct all-day event counts', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    $today = Carbon::now()->startOfDay();
    $tomorrow = Carbon::now()->addDay()->startOfDay();

    // Create all-day events
    eventFactory(2)
        ->for($calendar)
        ->state(new Sequence(
            ['all_day' => true, 'starts_at' => $today, 'ends_at' => null],
            ['all_day' => true, 'starts_at' => $tomorrow, 'ends_at' => null],
        ))
        ->create();

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('totalEvents', 2)
            ->where('eventsToday', 1)
            ->where('eventsTomorrow', 1)
        );
});

it('only shows authenticated user events', function (): void {
    actingAsUser();

    eventFactory(5)->create();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    eventFactory(3)->for($otherCalendar)->create();

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('totalEvents', 5)
            ->where('totalCalendars', 1)
        );
});

it('shows calendars count for multiple calendars', function (): void {
    actingAsUser();

    $calendar1 = calendarFactory()->create();
    $calendar2 = calendarFactory()->create();
    $calendar3 = calendarFactory()->create();

    eventFactory(3)->for($calendar1)->create();
    eventFactory(2)->for($calendar2)->create();
    eventFactory(4)->for($calendar3)->create();

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('totalEvents', 9)
            ->where('totalCalendars', 3)
        );
});

it('calculates average events per day of week correctly', function (): void {
    actingAsUser();

    $today = Carbon::now()->startOfDay()->toImmutable();

    // Create multiple events on different days of the week
    // Sunday = 0, Monday = 1, ..., Saturday = 6
    $sunday = $today->startOfWeek()->subDay();
    eventFactory(3)->create(['starts_at' => $sunday->addHours(10)]);

    $monday = $today->startOfWeek();
    eventFactory(5)->create(['starts_at' => $monday->addHours(10)]);

    $wednesday = $today->startOfWeek()->addDays(2);
    eventFactory(2)->create(['starts_at' => $wednesday->addHours(10)]);

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('averagePerDayOfWeek', fn ($value): bool => count($value) === 7 &&
                $value[0] === 3 &&
                $value[1] === 5 &&
                $value[2] === 0 &&
                $value[3] === 2 &&
                $value[4] === 0 &&
                $value[5] === 0 &&
                $value[6] === 0)
        );
});
