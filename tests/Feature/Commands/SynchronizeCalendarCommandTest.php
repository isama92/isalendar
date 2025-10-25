<?php

use App\Actions\SynchronizeCalendar;
use App\Console\Commands\SynchronizeCalendarCommand;
use App\Models\Calendar;

use function Pest\Laravel\artisan;
use function Pest\Laravel\partialMock;
use function Tests\Helpers\calendarFactory;

uses()->group('commands');

beforeEach(function (): void {
    test()->confirmationText = 'Are you sure you want to run this command?';
    test()->disabledText = 'This calendar is disabled. Are you sure you want to synchronize it?';
});

it('executes synchronize calendar action for the calendar', function (): void {
    $calendar = calendarFactory()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->once()
        ->withArgs(fn ($cal): bool => $cal instanceof Calendar && $cal->id === $calendar->id)
        ->andReturnUndefined();

    artisan(SynchronizeCalendarCommand::class, [
        'calendar' => $calendar->id,
    ])
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->assertSuccessful();
});

it('fails if calendar does not exist', function (): void {
    artisan(SynchronizeCalendarCommand::class, [
        'calendar' => 1,
    ])
        ->expectsOutput('Calendar does not exist')
        ->assertFailed();
});

it('stops if user decides not to execute a disabled calendar', function (): void {
    $calendar = calendarFactory()->disabled()->create();

    artisan(SynchronizeCalendarCommand::class, [
        'calendar' => $calendar->id,
    ])
        ->expectsConfirmation(test()->disabledText, 'No')
        ->assertSuccessful();
});

it('executes synchronize calendar if user decides to execute a disabled calendar', function (): void {
    $calendar = calendarFactory()->disabled()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->once()
        ->withArgs(fn ($cal): bool => $cal instanceof Calendar && $cal->id === $calendar->id)
        ->andReturnUndefined();

    artisan(SynchronizeCalendarCommand::class, [
        'calendar' => $calendar->id,
    ])
        ->expectsConfirmation(test()->disabledText, 'Yes')
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->assertSuccessful();
});

it('does not ask for confirmation if force option is used', function (): void {
    $calendar = calendarFactory()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->once()
        ->withArgs(fn ($cal): bool => $cal instanceof Calendar && $cal->id === $calendar->id)
        ->andReturnUndefined();

    artisan(SynchronizeCalendarCommand::class, [
        'calendar' => $calendar->id,
        '--force' => true,
    ])
        ->assertSuccessful();
});

it('asks for confirmation before executing a disabled calendar if force option is used', function (): void {
    $calendar = calendarFactory()->disabled()->create();

    partialMock(SynchronizeCalendar::class)
        ->shouldReceive('execute')
        ->once()
        ->withArgs(fn ($cal): bool => $cal instanceof Calendar && $cal->id === $calendar->id)
        ->andReturnUndefined();

    artisan(SynchronizeCalendarCommand::class, [
        'calendar' => $calendar->id,
        '--force' => true,
    ])
        ->expectsConfirmation(test()->disabledText, 'Yes')
        ->assertSuccessful();
});
