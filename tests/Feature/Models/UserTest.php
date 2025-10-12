<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\User;

uses()->group('models');

it('can access calendars through connectors', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();

    $connector->calendars()->attach($calendar);

    expect($user->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($calendar->id);
});

it('can access multiple calendars from multiple connectors', function (): void {
    $user = User::factory()->create();

    $connector1 = Connector::factory()->for($user)->create();
    $connector2 = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();
    $calendar3 = Calendar::factory()->create();

    $connector1->calendars()->attach([$calendar1->id, $calendar2->id]);
    $connector2->calendars()->attach($calendar3->id);

    expect($user->calendars)
        ->toHaveCount(3)
        ->pluck('id')->toArray()->toContain($calendar1->id, $calendar2->id, $calendar3->id);
});

it('only accesses calendars from own connectors', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = Connector::factory()->for($user1)->create();
    $connector2 = Connector::factory()->for($user2)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();

    $connector1->calendars()->attach($calendar1);
    $connector2->calendars()->attach($calendar2);

    expect($user1->refresh()->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($calendar1->id)
        ->and($user2->refresh()->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($calendar2->id);
});

it('does not return duplicate calendars when shared across multiple connectors', function (): void {
    $user = User::factory()->create();

    $connector1 = Connector::factory()->for($user)->create();
    $connector2 = Connector::factory()->for($user)->create();

    $sharedCalendar = Calendar::factory()->create();

    $connector1->calendars()->attach($sharedCalendar);
    $connector2->calendars()->attach($sharedCalendar);

    expect($user->refresh()->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($sharedCalendar->id);
});

it('returns empty collection when user has no connectors', function (): void {
    $user = User::factory()->create();

    expect($user->calendars)->toBeEmpty();
});

it('returns empty collection when connectors have no calendars', function (): void {
    $user = User::factory()->create();
    Connector::factory()->for($user)->create();

    expect($user->calendars)->toBeEmpty();
});
