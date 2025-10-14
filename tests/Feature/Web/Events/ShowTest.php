<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'events');

it('displays event for authenticated user', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $event = Event::factory()->for($calendar)->create();

    actingAs($user);

    get(route('events.show', $event))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Show')
            ->has('event')
            ->where('event.title', $event->title)
            ->where('event.description', $event->description)
            ->where('event.all_day', $event->all_day)
            ->where('event.calendar.name', $calendar->name)
        );
});

it('redirects unauthenticated users to login', function (): void {
    $calendar = Calendar::factory()->create();
    $event = Event::factory()->for($calendar)->create();

    get(route('events.show', $event))
        ->assertRedirect(route('login'));
});

it('prevents user from viewing another users event', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    $otherEvent = Event::factory()->for($otherCalendar)->create();

    actingAs($user);

    get(route('events.show', $otherEvent))
        ->assertForbidden();
});
