<?php

use App\Actions\SynchronizeConnector;
use App\Console\Commands\SynchronizeConnectorCommand;
use App\Models\Connector;

use function Pest\Laravel\artisan;
use function Pest\Laravel\partialMock;

uses()->group('commands');

beforeEach(function (): void {
    test()->confirmationText = 'Are you sure you want to run this command?';
});

it('executes synchronize connector action', function (): void {
    $connector = Connector::factory()->create();

    partialMock(SynchronizeConnector::class)
        ->shouldReceive('execute')
        ->once()
        ->withAnyArgs()
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
        ->expectsConfirmation('This connector is disabled. Are you sure you want to synchronize it?', 'No')
        ->assertSuccessful();
});

it('executes synchronize connector if user decides to execute a disabled connector', function (): void {
    $connector = Connector::factory()->disabled()->create();

    partialMock(SynchronizeConnector::class)
        ->shouldReceive('execute')
        ->once()
        ->withAnyArgs()
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
    ])
        ->expectsConfirmation('This connector is disabled. Are you sure you want to synchronize it?', 'Yes')
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->assertSuccessful();
});

it('does not ask for confirmation if force option is used', function (): void {
    $connector = Connector::factory()->create();

    partialMock(SynchronizeConnector::class)
        ->shouldReceive('execute')
        ->once()
        ->withAnyArgs()
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
        '--force' => true,
    ])
        ->assertSuccessful();
});

it('asks for confirmation before executing a disabled connector if force option is used', function (): void {
    $connector = Connector::factory()->disabled()->create();

    partialMock(SynchronizeConnector::class)
        ->shouldReceive('execute')
        ->once()
        ->withAnyArgs()
        ->andReturnUndefined();

    artisan(SynchronizeConnectorCommand::class, [
        'connector' => $connector->id,
        '--force' => true,
    ])
        ->expectsConfirmation('This connector is disabled. Are you sure you want to synchronize it?', 'Yes')
        ->assertSuccessful();
});
