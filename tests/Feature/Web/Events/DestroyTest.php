<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;

uses()->group('web', 'events');

it('allows user to delete their event', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $event = Event::factory()->for($calendar)->create();

    actingAs($user);

    delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('success');

    assertSoftDeleted('events', ['id' => $event->id]);
});

it('prevents unauthenticated user from deleting event', function (): void {
    $calendar = Calendar::factory()->create();
    $event = Event::factory()->for($calendar)->create();

    delete(route('events.destroy', $event))
        ->assertRedirect(route('login'));

    assertNotSoftDeleted('events', ['id' => $event->id]);
});

it('prevents user from deleting another users event', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    $otherEvent = Event::factory()->for($otherCalendar)->create();

    actingAs($user);

    delete(route('events.destroy', $otherEvent))
        ->assertForbidden();

    assertNotSoftDeleted('events', ['id' => $otherEvent->id]);
});
