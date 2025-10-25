<?php

use App\Actions\SynchronizeCalendar;
use App\Console\Commands\SynchronizeConnectorCommand;
use App\Models\Calendar;
use App\Models\Connector;

use function Pest\Laravel\artisan;
use function Pest\Laravel\partialMock;
use function Tests\Helpers\calendarFactory;

uses()->group('commands');

beforeEach(function (): void {
    test()->confirmationText = 'Are you sure you want to run this command?';
    test()->disabledText = 'This connector is disabled. Are you sure you want to synchronize it?';
});

it('executes synchronize calendar action for each enabled calendar', function (): void {
    $connector = Connector::factory()->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->disabled()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->times(2)
        ->withArgs(fn ($calendar): bool => $calendar instanceof Calendar)
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
    ])
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->assertSuccessful();
});

it('fails if connector does not exist', function (): void {
    artisan(SynchronizeConnectorCommand::class, [
        'connector' => 1,
    ])
        ->expectsOutput('Connector does not exist')
        ->assertFailed();
});

it('stops if user decides not to execute a disabled connector', function (): void {
    $connector = Connector::factory()->disabled()->create();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
    ])
        ->expectsConfirmation(test()->disabledText, 'No')
        ->assertSuccessful();
});

it('executes synchronize connector if user decides to execute a disabled connector', function (): void {
    $connector = Connector::factory()->disabled()->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->disabled()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->times(2)
        ->withArgs(fn ($calendar): bool => $calendar instanceof Calendar)
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
    ])
        ->expectsConfirmation(test()->disabledText, 'Yes')
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->assertSuccessful();
});

it('does not ask for confirmation if force option is used', function (): void {
    $connector = Connector::factory()->create();
    calendarFactory(connector: $connector)->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->once()
        ->withArgs(fn ($calendar): bool => $calendar instanceof Calendar)
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
        '--force' => true,
    ])
        ->assertSuccessful();
});

it('asks for confirmation before executing a disabled connector if force option is used', function (): void {
    $connector = Connector::factory()->disabled()->create();
    calendarFactory(connector: $connector)->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->once()
        ->withArgs(fn ($calendar): bool => $calendar instanceof Calendar)
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
        '--force' => true,
    ])
        ->expectsConfirmation(test()->disabledText, 'Yes')
        ->assertSuccessful();
});

it('only synchronizes enabled calendars', function (): void {
    $connector = Connector::factory()->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->disabled()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->times(2)
        ->withArgs(fn ($calendar): bool => $calendar instanceof Calendar && $calendar->enabled)
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
        '--force' => true,
    ])
        ->assertSuccessful();
});

it('succeeds when connector has no calendars', function (): void {
    $connector = Connector::factory()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->never();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
        '--force' => true,
    ])
        ->assertSuccessful();
});
