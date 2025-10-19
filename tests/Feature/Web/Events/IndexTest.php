<?php

use App\Enums\Service;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('redirects unauthenticated users to login', function (): void {
    get(route('events.index'))
        ->assertRedirect(route('login'));
});

it('shows correct events with properties, calendar names, and without pagination', function (): void {
    actingAsUser();
    $calendar1 = calendarFactory()->create(['name' => 'Work Calendar']);
    $calendar2 = calendarFactory()->create(['name' => 'Personal Calendar']);

    eventFactory()->for($calendar1)->create([
        'title' => 'Work Event',
        'all_day' => true,
        'description' => 'Event description',
    ]);
    eventFactory(25)->for($calendar2)->create();

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('events', 26)
            ->where('events.0.all_day', true)
            ->where('events.0.description', 'Event description')
            ->where('events.0.calendar.name', 'Work Calendar')
            ->where('events.1.calendar.name', 'Personal Calendar')
        );
});

it('only shows authenticated user events', function (): void {
    actingAsUser();

    $userEvent = eventFactory()->create();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    eventFactory()->for($otherCalendar)->create();

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('events', 1)
            ->where('events.0.id', $userEvent->id)
        );
});

it('shows enabled providers', function (): void {
    Provider::forceCreate([
        'name' => 'disabled',
        'image_path' => '',
        'access_object' => '',
        'auth_route' => '',
        'enabled' => false,
    ]);

    actingAsUser();

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('providers', 1)
            ->where('providers.0.id', Service::GOOGLE_CALENDAR->id())
        );
});

it('shows events from multiple connectors', function (): void {
    actingAsUser();

    $connector1 = connectorFactory()->for(Provider::googleCalendar())->create();
    $calendar1 = calendarFactory(connector: $connector1)->create();
    eventFactory()->for($calendar1)->create();

    $connector2 = connectorFactory()->for(Provider::microsoftOutlook())->create();
    $calendar2 = calendarFactory(connector: $connector2)->create();
    eventFactory()->for($calendar2)->create();

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('events', 2)
        );
});
