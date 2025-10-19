<?php

use App\Models\Calendar;
use App\Models\User;
use App\Models\UserSetting;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('models');

it('can access calendars through connectors', function (): void {
    $user = actingAsUser();
    $connector = connectorFactory()->create();
    $calendar = calendarFactory(connector: $connector)->create();

    expect($user->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($calendar->id);
});

it('can access multiple calendars from multiple connectors', function (): void {
    $user = actingAsUser();

    $connector1 = connectorFactory()->create();
    $connector2 = connectorFactory()->create();

    $calendar1 = calendarFactory(connector: $connector1)->create();
    $calendar2 = calendarFactory(connector: $connector1)->create();
    $calendar3 = calendarFactory(connector: $connector2)->create();

    expect($user->calendars)
        ->toHaveCount(3)
        ->pluck('id')->toArray()->toContain($calendar1->id, $calendar2->id, $calendar3->id);
});

it('only accesses calendars from own connectors', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = connectorFactory()->for($user1)->create();
    $connector2 = connectorFactory()->for($user2)->create();

    $calendar1 = calendarFactory(connector: $connector1)->create();
    $calendar2 = calendarFactory(connector: $connector2)->create();

    expect($user1->refresh()->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($calendar1->id)
        ->and($user2->refresh()->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($calendar2->id);
});

it('does not return duplicate calendars when shared across multiple connectors', function (): void {
    $user = actingAsUser();

    $connector1 = connectorFactory()->create();
    $connector2 = connectorFactory()->create();

    $sharedCalendar = Calendar::factory()
        ->hasAttached([$connector1, $connector2])
        ->create();

    expect($user->refresh()->calendars)
        ->toHaveCount(1)
        ->first()->id->toBe($sharedCalendar->id);
});

it('returns empty collection when user has no connectors', function (): void {
    $user = actingAsUser();

    expect($user->calendars)->toBeEmpty();
});

it('returns empty collection when connectors have no calendars', function (): void {
    $user = actingAsUser();
    connectorFactory()->create();

    expect($user->calendars)->toBeEmpty();
});

it('user settings are automatically created on user creation', function (): void {
    $user = actingAsUser();

    expect($user->settings)
        ->not->toBeNull()
        ->toBeInstanceOf(UserSetting::class)
        ->default_timezone->toBe(config('app.default_user_timezone'));
});

it('uses user photo_path attribute', function (): void {
    $user = actingAsUser();

    expect($user->photo_path)->toBe("{$user->id}.webp");
});
