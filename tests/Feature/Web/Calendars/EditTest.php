<?php

use App\Models\Calendar;
use App\Models\User;

use function Pest\Laravel\put;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'calendars');

it('can disable an enabled calendar', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    put(route('calendars.update', $calendar), [
        'enabled' => false,
    ])
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    expect($calendar->fresh())->enabled->toBeFalse();
});

it('can enable a disabled calendar', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->disabled()->create();

    put(route('calendars.update', $calendar), [
        'enabled' => true,
    ])
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    expect($calendar->fresh())->enabled->toBeTrue();
});

it('prevents unauthenticated user from updating calendar', function (): void {
    $calendar = Calendar::factory()->create(['enabled' => true]);

    put(route('calendars.update', $calendar), [
        'enabled' => false,
    ])
        ->assertRedirect(route('login'));

    expect($calendar->fresh())->enabled->toBeTrue();
});

it('prevents user from updating another users calendar', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();

    put(route('calendars.update', $otherCalendar), [
        'enabled' => false,
    ])
        ->assertForbidden();

    expect($otherCalendar->fresh())->enabled->toBeTrue();
});

it('requires enabled field', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    put(route('calendars.update', $calendar), [])
        ->assertSessionHasErrors('enabled');

    expect($calendar->fresh())->enabled->toBeTrue();
});

it('validates enabled field must be boolean', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    put(route('calendars.update', $calendar), [
        'enabled' => 'invalid',
    ])
        ->assertSessionHasErrors('enabled');

    expect($calendar->fresh())->enabled->toBeTrue();
});
