<?php

use App\Models\Calendar;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('models');

it('loads the connector for the logged in user with hidden access details', function (): void {
    actingAsUser();
    $connector = connectorFactory()->create();
    calendarFactory(connector: $connector)->create();

    expect(Calendar::sole())
        ->relationLoaded('connectorsForUser')->toBeTrue()
        ->connectorsForUser->toHaveCount(1)
        ->connectorsForUser->sole()->is($connector)->toBeTrue()
        ->connector?->is($connector)->toBeTrue()
        ->connector->toArray()->not()->toHaveKey('access_details');
});

it('can access to the authenticated user connector', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = connectorFactory()->for($user1)->create();
    $connector2 = connectorFactory()->for($user2)->create();

    Calendar::factory()
        ->hasAttached([$connector1, $connector2])
        ->create();

    actingAs($user2);
    expect(Calendar::sole()->connector->id)->toBe($connector2->id);

    actingAs($user1);
    expect(Calendar::sole()->connector->id)->toBe($connector1->id);
});

it('can access users through connectors', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = connectorFactory()->for($user1)->create();
    $connector2 = connectorFactory()->for($user2)->create();

    $calendar = Calendar::factory()
        ->hasAttached([$connector1, $connector2])
        ->create();

    expect($calendar->users)
        ->toHaveCount(2)
        ->pluck('id')->toArray()->toContain($user1->id, $user2->id);
});

it('can access multiple users when shared across connectors', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    $connector1 = connectorFactory()->for($user1)->create();
    $connector2 = connectorFactory()->for($user2)->create();
    $connector3 = connectorFactory()->for($user3)->create();

    $calendar = Calendar::factory()
        ->hasAttached([$connector1, $connector2, $connector3])
        ->create();

    expect($calendar->refresh()->users)
        ->toHaveCount(3)
        ->pluck('id')->toArray()->toContain($user1->id, $user2->id, $user3->id);
});

it('only includes users who have connectors attached to it', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = connectorFactory()->for($user1)->create();
    connectorFactory()->for($user2)->create();

    $calendar = Calendar::factory()
        ->hasAttached($connector1)
        ->create();

    expect($calendar->refresh()->users)
        ->toHaveCount(1)
        ->first()->id->toBe($user1->id);
});

it('does not return duplicate users when user has multiple connectors attached', function (): void {
    $user = actingAsUser();

    $connector1 = connectorFactory()->create();
    $connector2 = connectorFactory()->create();

    $calendar = Calendar::factory()
        ->hasAttached([$connector1, $connector2])
        ->create();

    expect($calendar->refresh()->users)
        ->toHaveCount(1)
        ->first()->id->toBe($user->id);
});

it('returns empty collection when no connectors are attached', function (): void {
    $calendar = Calendar::factory()->create();

    expect($calendar->users)->toBeEmpty();
});

it('can access users and their connectors relationship data', function (): void {
    $user = actingAsUser();
    $connector = connectorFactory()->create();

    $calendar = Calendar::factory()
        ->hasAttached($connector)
        ->create();

    expect($calendar->refresh()->users->firstOrFail())
        ->id->toBe($user->id)
        ->email->toBe($user->email);
});

it('can access provider through connectors', function (): void {
    $provider = Provider::googleCalendar();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $connector1 = connectorFactory()->for($user1)->for($provider)->create();
    $connector2 = connectorFactory()->for($user2)->for($provider)->create();

    Calendar::factory()
        ->hasAttached([$connector1, $connector2])
        ->create();

    expect(Calendar::sole()->provider)
        ->not->toBeNull()
        ->id->toBe($provider->id);
});

it('sets enabled to true when calendar is restored', function (): void {
    $calendar = Calendar::factory()->create(['enabled' => false]);
    $calendar->delete();

    expect($calendar->trashed())->toBeTrue()
        ->and($calendar->enabled)->toBeFalse();

    $calendar->restore();

    expect($calendar->trashed())->toBeFalse()
        ->and($calendar->fresh()->enabled)->toBeTrue();
});
