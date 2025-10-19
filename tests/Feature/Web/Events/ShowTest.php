<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('displays event for authenticated user', function (): void {
    actingAsUser();
    $event = eventFactory()->create();

    get(route('events.show', $event))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Show')
            ->has('event')
            ->where('event.title', $event->title)
            ->where('event.description', $event->description)
            ->where('event.all_day', $event->all_day)
            ->where('event.calendar.name', $event->calendar->name)
        );
});

it('redirects unauthenticated users to login', function (): void {
    $event = eventFactory()->create();

    get(route('events.show', $event))
        ->assertRedirect(route('login'));
});

it('prevents user from viewing another users event', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherEvent = eventFactory()->for($otherCalendar)->create();

    get(route('events.show', $otherEvent))
        ->assertForbidden();
});
