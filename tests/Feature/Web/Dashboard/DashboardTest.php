<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('displays stats for authenticated user', function (): void {
    $user = User::factory()->create();

    actingAs($user);

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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();
    $tomorrow = Carbon::now()->addDay()->startOfDay();
    $thisWeekStart = Carbon::now()->startOfWeek();
    $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();

    // Create events for different time periods
    Event::factory()->for($calendar)->create(['starts_at' => $today->copy()->addHours(10)]); // Today
    Event::factory()->for($calendar)->create(['starts_at' => $tomorrow->copy()->addHours(10)]); // Tomorrow
    Event::factory()->for($calendar)->create(['starts_at' => $thisWeekStart->copy()->addDays(2)]); // This week
    Event::factory()->for($calendar)->create(['starts_at' => $nextWeekStart->copy()->addDays(2)]); // Next week

    actingAs($user);

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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();
    $tomorrow = Carbon::now()->addDay()->startOfDay();

    // Create all-day events
    Event::factory()->for($calendar)->create([
        'all_day' => true,
        'starts_at' => $today,
        'ends_at' => null,
    ]);
    Event::factory()->for($calendar)->create([
        'all_day' => true,
        'starts_at' => $tomorrow,
        'ends_at' => null,
    ]);

    actingAs($user);

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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();
    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    Event::factory()->count(5)->for($calendar)->create();
    Event::factory()->count(3)->for($otherCalendar)->create();

    actingAs($user);

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('totalEvents', 5)
            ->where('totalCalendars', 1)
        );
});

it('shows calendars count for multiple calendars', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar1->connectors()->attach($connector);

    $calendar2 = Calendar::factory()->create();
    $calendar2->connectors()->attach($connector);

    $calendar3 = Calendar::factory()->create();
    $calendar3->connectors()->attach($connector);

    Event::factory()->count(3)->for($calendar1)->create();
    Event::factory()->count(2)->for($calendar2)->create();
    Event::factory()->count(4)->for($calendar3)->create();

    actingAs($user);

    get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('totalEvents', 9)
            ->where('totalCalendars', 3)
        );
});

it('calculates average events per day of week correctly', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $today = Carbon::now()->startOfDay();

    // Create multiple events on different days of the week
    // Sunday = 0, Monday = 1, ..., Saturday = 6
    $sunday = $today->copy()->startOfWeek()->subDay();
    Event::factory()->count(3)->for($calendar)->create(['starts_at' => $sunday->copy()->addHours(10)]);

    $monday = $today->copy()->startOfWeek();
    Event::factory()->count(5)->for($calendar)->create(['starts_at' => $monday->copy()->addHours(10)]);

    $wednesday = $today->copy()->startOfWeek()->addDays(2);
    Event::factory()->count(2)->for($calendar)->create(['starts_at' => $wednesday->copy()->addHours(10)]);

    actingAs($user);

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
