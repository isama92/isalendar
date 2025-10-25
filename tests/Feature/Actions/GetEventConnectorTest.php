<?php

use App\Actions\GetEventConnector;
use App\Models\Connector;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('actions');

it('returns the creator connector when it exists and is enabled', function (): void {
    $event = eventFactory()->create();

    $result = app(GetEventConnector::class)->execute($event);

    expect($result)
        ->toBeInstanceOf(Connector::class)
        ->id->toBe(Connector::sole()->id);
});

it('returns a fallback connector when creator connector is disabled', function (): void {
    $disabledConnector = connectorFactory()->disabled()->create();
    $enabledConnector = connectorFactory()->create();

    $calendar = calendarFactory(connector: $disabledConnector)->create();
    $calendar->connectors()->attach($enabledConnector->id);

    $event = eventFactory()
        ->for($calendar)
        ->create(['created_by_connector_id' => $disabledConnector->id]);

    $result = app(GetEventConnector::class)->execute($event);

    expect($result)
        ->toBeInstanceOf(Connector::class)
        ->id->toBe($enabledConnector->id);
});

it('returns a fallback connector when creator connector does not exist', function (): void {
    $event = eventFactory()
        ->create(['created_by_connector_id' => null]);

    $result = app(GetEventConnector::class)->execute($event);

    expect($result)
        ->toBeInstanceOf(Connector::class)
        ->id->toBe(Connector::sole()->id);
});

it('returns null when no enabled connectors are available', function (): void {
    $disabledConnector = connectorFactory()->disabled()->create();
    $calendar = calendarFactory(connector: $disabledConnector)->create();
    $event = eventFactory()
        ->for($calendar)
        ->create(['created_by_connector_id' => null]);

    $result = app(GetEventConnector::class)->execute($event);

    expect($result)->toBeNull();
});

it('returns the first enabled connector when multiple are available', function (): void {
    $connector1 = connectorFactory()->create();
    $connector2 = connectorFactory()->create();

    $calendar = calendarFactory(connector: $connector1)->create();
    $calendar->connectors()->attach($connector2->id);

    $event = eventFactory()
        ->for($calendar)
        ->create(['created_by_connector_id' => null]);

    $result = app(GetEventConnector::class)->execute($event);

    expect($result)
        ->toBeInstanceOf(Connector::class)
        ->and($result?->id)
        ->toBeIn([$connector1->id, $connector2->id]);
});

it('prefers creator connector over other enabled connectors', function (): void {
    $creatorConnector = connectorFactory()->create();
    $otherConnector = connectorFactory()->create();

    $calendar = calendarFactory(connector: $creatorConnector)->create();
    $calendar->connectors()->attach($otherConnector->id);

    $event = eventFactory()
        ->for($calendar)
        ->create(['created_by_connector_id' => $creatorConnector->id]);

    $result = app(GetEventConnector::class)->execute($event);

    expect($result)
        ->toBeInstanceOf(Connector::class)
        ->id->toBe($creatorConnector->id);
});
