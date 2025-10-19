<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('displays edit page with event data for authenticated user', function (): void {
    actingAsUser();

    $calendar1 = calendarFactory()->create(['name' => 'Calendar 1']);
    $calendar2 = calendarFactory()->disabled()->create(['name' => 'Calendar 2']);

    $event = eventFactory()->for($calendar1)->create([
        'title' => 'Test Event',
        'description' => 'Test Description',
    ]);

    get(route('events.edit', $event))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Form')
            ->has('timezones')
            ->has('event')
            ->where('event.id', $event->id)
            ->where('event.title', 'Test Event')
            ->where('event.description', 'Test Description')
            ->where('event.calendar_id', $calendar1->id)
            ->has('calendars', 2)
            ->where('calendars.0.id', $calendar1->id)
            ->where('calendars.1.id', $calendar2->id)
        );
});

it('redirects unauthenticated users to login', function (): void {
    $event = eventFactory()->create();

    get(route('events.edit', $event))
        ->assertRedirect(route('login'));
});

it('prevents user from editing another users event', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherEvent = eventFactory()->for($otherCalendar)->create();

    get(route('events.edit', $otherEvent))
        ->assertForbidden();
});
