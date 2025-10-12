<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

uses()->group('web', 'calendars');

it('allows user to delete their calendar', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    delete(route('calendars.destroy', $calendar))
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    expect($calendar->connectors())
        ->count()->toBe(0);
});

it('soft deletes calendar when no connectors remain', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    delete(route('calendars.destroy', $calendar));

    expect(Calendar::find($calendar->id))->toBeNull()
        ->and(Calendar::withTrashed()->find($calendar->id))->not->toBeNull()
        ->and(Calendar::withTrashed()->find($calendar->id)->trashed())->toBeTrue();
});

it('does not delete calendar when other connectors remain', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = Connector::factory()->for($user1)->create();
    $connector2 = Connector::factory()->for($user2)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach([$connector1->id, $connector2->id]);

    actingAs($user1);

    delete(route('calendars.destroy', $calendar));

    expect($calendar->fresh())
        ->not->toBeNull()
        ->and($calendar->connectors())
        ->count()->toBe(1)
        ->first()->id->toBe($connector2->id);
});

it('deletes connector when no calendars remain', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    delete(route('calendars.destroy', $calendar));

    expect(Connector::find($connector->id))
        ->toBeNull();
});

it('does not delete connector when other calendars remain', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();

    $calendar1->connectors()->attach($connector);
    $calendar2->connectors()->attach($connector);

    actingAs($user);

    delete(route('calendars.destroy', $calendar1));

    expect(Connector::find($connector->id))
        ->not->toBeNull()
        ->and($connector->calendars())
        ->count()->toBe(1)
        ->first()->id->toBe($calendar2->id);
});

it('prevents unauthenticated user from deleting calendar', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    delete(route('calendars.destroy', $calendar))
        ->assertRedirect(route('login'));

    expect($calendar->connectors())
        ->count()->toBe(1);
});

it('prevents user from deleting another users calendar', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    actingAs($user);

    delete(route('calendars.destroy', $otherCalendar))
        ->assertForbidden();

    expect($otherCalendar->connectors())
        ->count()->toBe(1);
});

it('detaches calendar from connector on deletion', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    expect($calendar->connectors())
        ->count()->toBe(1);

    delete(route('calendars.destroy', $calendar));

    expect($calendar->connectors())
        ->count()->toBe(0);
});

it('soft deletes all events when calendar is deleted', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    Event::factory()->count(5)->for($calendar)->create();

    actingAs($user);

    expect($calendar->events())
        ->count()->toBe(5);

    delete(route('calendars.destroy', $calendar));

    expect(Event::where('calendar_id', $calendar->id))
        ->count()->toBe(0);
});
