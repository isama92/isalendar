<?php

use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('allows user to delete their event with external_id', function (): void {
    actingAsUser();
    $event = eventFactory()->create(['external_id' => 'google-event-123']);

    delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('success');

    expect(Event::withMarkedForDeletion()->find($event->id))
        ->marked_for_deletion_at->not->toBeNull();
});

it('allows user to delete their event without external_id', function (): void {
    actingAsUser();
    $event = eventFactory()->create(['external_id' => null]);

    delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('success');

    assertSoftDeleted('events', ['id' => $event->id]);
});

it('prevents unauthenticated user from deleting event', function (): void {
    $event = eventFactory()->create();

    delete(route('events.destroy', $event))
        ->assertRedirect(route('login'));

    assertNotSoftDeleted('events', ['id' => $event->id]);
});

it('prevents user from deleting another users event', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherEvent = eventFactory()->for($otherCalendar)->create();

    delete(route('events.destroy', $otherEvent))
        ->assertForbidden();

    assertNotSoftDeleted('events', ['id' => $otherEvent->id]);
});
