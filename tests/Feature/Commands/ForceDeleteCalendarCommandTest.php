<?php

use App\Console\Commands\ForceDeleteCalendarCommand;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\eventFactory;

uses()->group('commands');

beforeEach(function (): void {
    test()->confirmationText = 'Are you sure you want to run this command?';
});

it('force deletes a calendar and its events', function (): void {
    $calendar = calendarFactory()->create();
    $events = eventFactory(3)->for($calendar)->create();

    artisan(ForceDeleteCalendarCommand::class, [
        'calendar' => $calendar->id,
    ])
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->assertSuccessful()
        ->expectsOutput('Calendar force deleted successfully.');

    assertDatabaseMissing('calendars', ['id' => $calendar->id]);
    assertDatabaseMissing('events', ['id' => $events->get(0)->id]);
    assertDatabaseMissing('events', ['id' => $events->get(1)->id]);
    assertDatabaseMissing('events', ['id' => $events->get(2)->id]);
});

it('fails if calendar does not exist', function (): void {
    artisan(ForceDeleteCalendarCommand::class, [
        'calendar' => 1,
    ])
        ->expectsOutput('Calendar does not exist')
        ->assertFailed();
});

it('stops if user decides not to force delete the calendar', function (): void {
    $calendar = calendarFactory()->create();

    artisan(ForceDeleteCalendarCommand::class, [
        'calendar' => $calendar->id,
    ])
        ->expectsConfirmation(test()->confirmationText, 'No')
        ->assertSuccessful();

    assertDatabaseHas('calendars', ['id' => $calendar->id]);
});

it('does not ask for confirmation if force option is used', function (): void {
    $calendar = calendarFactory()->create();

    artisan(ForceDeleteCalendarCommand::class, [
        'calendar' => $calendar->id,
        '--force' => true,
    ])
        ->assertSuccessful()
        ->expectsOutput('Calendar force deleted successfully.');

    assertDatabaseMissing('calendars', ['id' => $calendar->id]);
});

it('force deletes a soft deleted calendar', function (): void {
    $calendar = calendarFactory()->create();
    $calendar->delete();

    expect($calendar->trashed())->toBeTrue();

    artisan(ForceDeleteCalendarCommand::class, [
        'calendar' => $calendar->id,
    ])
        ->expectsConfirmation(test()->confirmationText, 'yes')
        ->expectsOutput('This calendar is already soft deleted.')
        ->assertSuccessful()
        ->expectsOutput('Calendar force deleted successfully.');

    assertDatabaseMissing('calendars', ['id' => $calendar->id]);
});
