<?php

use App\Actions\GetThrottleLimitKey;
use App\Enums\Service;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'calendars');

it('displays calendars for authenticated user', function (): void {
    actingAsUser();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars')
            ->has('providers')
        );
});

it('redirects unauthenticated users to login', function (): void {
    get(route('calendars.index'))
        ->assertRedirect(route('login'));
});

it('shows correct calendars with event counts', function (): void {
    actingAsUser();
    $connector = connectorFactory()->create();

    $calendar1 = calendarFactory(connector: $connector)->create();
    $calendar2 = calendarFactory(connector: $connector)->create();

    eventFactory(5)->for($calendar1)->create();
    eventFactory(3)->for($calendar2)->create();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 2)
            ->where('calendars.0.events_count', 5)
            ->where('calendars.1.events_count', 3)
        );
});

it('only shows authenticated user calendars', function (): void {
    actingAsUser();
    $userCalendar = calendarFactory()->create();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    calendarFactory(connector: $otherConnector)->create();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 1)
            ->where('calendars.0.id', $userCalendar->id)
        );
});

it('shows enabled providers', function (): void {
    actingAsUser();
    Provider::forceCreate([
        'name' => 'disabled',
        'image_path' => '',
        'access_object' => '',
        'auth_route' => '',
        'enabled' => false,
    ]);

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('providers', 1)
            ->where('providers.0.id', Service::GOOGLE_CALENDAR->id())
        );
});

it('displays empty state when no calendars', function (): void {
    actingAsUser();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 0)
        );
});

it('returns all calendars for frontend pagination', function (): void {
    actingAsUser();

    // Create 15 calendars - all should be returned for frontend pagination
    calendarFactory(15)->create();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 15)
        );
});

it('shows zero event count for calendars without events', function (): void {
    actingAsUser();
    calendarFactory()->create(['name' => 'Empty Calendar']);

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 1)
            ->where('calendars.0.events_count', 0)
        );
});

it('shows multiple calendars for user with multiple connectors', function (): void {
    actingAsUser();

    $provider1 = Provider::googleCalendar();
    $provider2 = Provider::microsoftOutlook();

    $connector1 = connectorFactory()->for($provider1)->create();
    $connector2 = connectorFactory()->for($provider2)->create();

    calendarFactory(connector: $connector1)->create();
    calendarFactory(connector: $connector2)->create();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 2)
        );
});

it('includes can_sync status for each calendar', function (): void {
    actingAsUser();
    calendarFactory()->create();

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 1)
            ->where('calendars.0.can_sync', true)
        );
});

it('shows can_sync as false when calendar is rate limited', function (): void {
    $user = actingAsUser();
    $calendar = calendarFactory()->create();

    // Hit the rate limiter
    $key = app(GetThrottleLimitKey::class)->execute('ui-calendar-sync', [$user->id, $calendar->id]);
    RateLimiter::hit($key, 3600);

    get(route('calendars.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Calendars/Index')
            ->has('calendars', 1)
            ->where('calendars.0.can_sync', false)
        );
});
