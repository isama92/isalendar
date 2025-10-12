<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

uses()->group('web', 'calendars');

it('can disable an enabled calendar', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create(['enabled' => true]);
    $calendar->connectors()->attach($connector);

    actingAs($user);

    put(route('calendars.update', $calendar), [
        'enabled' => false,
    ])
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    expect($calendar->fresh())
        ->enabled->toBeFalse();
});

it('can enable a disabled calendar', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create(['enabled' => false]);
    $calendar->connectors()->attach($connector);

    actingAs($user);

    put(route('calendars.update', $calendar), [
        'enabled' => true,
    ])
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    expect($calendar->fresh())
        ->enabled->toBeTrue();
});

it('prevents unauthenticated user from updating calendar', function (): void {
    $calendar = Calendar::factory()->create(['enabled' => true]);

    put(route('calendars.update', $calendar), [
        'enabled' => false,
    ])
        ->assertRedirect(route('login'));

    expect($calendar->fresh())
        ->enabled->toBeTrue();
});

it('prevents user from updating another users calendar', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create(['enabled' => true]);
    $otherCalendar->connectors()->attach($otherConnector);

    actingAs($user);

    put(route('calendars.update', $otherCalendar), [
        'enabled' => false,
    ])
        ->assertForbidden();

    expect($otherCalendar->fresh())
        ->enabled->toBeTrue();
});

it('requires enabled field', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create(['enabled' => true]);
    $calendar->connectors()->attach($connector);

    actingAs($user);

    put(route('calendars.update', $calendar), [])
        ->assertSessionHasErrors('enabled');

    expect($calendar->fresh())
        ->enabled->toBeTrue();
});

it('validates enabled field must be boolean', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create(['enabled' => true]);
    $calendar->connectors()->attach($connector);

    actingAs($user);

    put(route('calendars.update', $calendar), [
        'enabled' => 'invalid',
    ])
        ->assertSessionHasErrors('enabled');

    expect($calendar->fresh())
        ->enabled->toBeTrue();
});
