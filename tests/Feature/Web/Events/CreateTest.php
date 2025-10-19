<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'events');

it('redirects unauthenticated users to login', function (): void {
    get(route('events.create'))
        ->assertRedirect(route('login'));
});

it('returns 403 when user has no connectors', function (): void {
    actingAsUser();

    get(route('events.create'))
        ->assertForbidden();
});

it('displays user calendars for selection', function (): void {
    actingAsUser();
    $calendar1 = calendarFactory()->create(['name' => 'Calendar 1']);
    $calendar2 = calendarFactory()->disabled()->create(['name' => 'Calendar 2']);

    get(route('events.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Form')
            ->has('calendars', 2)
            ->where('calendars.0.id', $calendar1->id)
            ->where('calendars.1.id', $calendar2->id)
            ->has('timezones')
        );
});

it('does not display other users calendars', function (): void {
    actingAsUser();

    $userCalendar = calendarFactory()->create();

    $otherConnector = connectorFactory()->for(User::factory())->create();

    calendarFactory(connector: $otherConnector)->create();

    get(route('events.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Form')
            ->has('calendars', 1)
            ->where('calendars.0.id', $userCalendar->id)
        );
});
