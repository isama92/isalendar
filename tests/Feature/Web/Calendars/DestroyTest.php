<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\delete;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'calendars');

it('allows user to delete their calendar', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    delete(route('calendars.destroy', $calendar))
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    expect($calendar->connectors())->count()->toBe(0);
});

it('soft deletes calendar when no connectors remain', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    delete(route('calendars.destroy', $calendar));

    expect(Calendar::find($calendar->id))->toBeNull()
        ->and(Calendar::withTrashed()->find($calendar->id))->not->toBeNull()
        ->and(Calendar::withTrashed()->find($calendar->id)->trashed())->toBeTrue();
});

it('does not delete calendar when other connectors remain', function (): void {
    actingAsUser();
    $user2 = User::factory()->create();

    $connector1 = connectorFactory()->create();
    $connector2 = connectorFactory()->for($user2)->create();

    $calendar = Calendar::factory()
        ->hasAttached([$connector1, $connector2])
        ->create();

    delete(route('calendars.destroy', $calendar));

    expect($calendar->fresh())
        ->not->toBeNull()
        ->and($calendar->connectors())
        ->count()->toBe(1)
        ->first()->id->toBe($connector2->id);
});

it('deletes connector when no calendars remain', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();
    $connector = $calendar->connectors->sole();

    delete(route('calendars.destroy', $calendar));

    expect(Connector::find($connector->id))->toBeNull();
});

it('does not delete connector when other calendars remain', function (): void {
    actingAsUser();
    $connector = connectorFactory()->create();

    $calendar1 = calendarFactory(connector: $connector)->create();
    $calendar2 = calendarFactory(connector: $connector)->create();

    delete(route('calendars.destroy', $calendar1));

    expect(Connector::find($connector->id))
        ->not->toBeNull()
        ->and($connector->calendars())
        ->count()->toBe(1)
        ->first()->id->toBe($calendar2->id);
});

it('prevents unauthenticated user from deleting calendar', function (): void {
    $calendar = calendarFactory()->create();

    delete(route('calendars.destroy', $calendar))
        ->assertRedirect(route('login'));

    expect($calendar->connectors())->count()->toBe(1);
});

it('prevents user from deleting another users calendar', function (): void {
    actingAsUser();
    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();

    delete(route('calendars.destroy', $otherCalendar))
        ->assertForbidden();

    expect($otherCalendar->connectors())->count()->toBe(1);
});

it('detaches calendar from connector on deletion', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    expect($calendar->connectors())->count()->toBe(1);

    delete(route('calendars.destroy', $calendar));

    expect($calendar->connectors())->count()->toBe(0);
});

it('soft deletes all events when calendar is deleted', function (): void {
    actingAsUser();
    $calendar = eventFactory(5)->create()
        ->first()->calendar;

    expect($calendar->events())->count()->toBe(5);

    delete(route('calendars.destroy', $calendar));

    expect(Event::where('calendar_id', $calendar->id))->count()->toBe(0);
});
