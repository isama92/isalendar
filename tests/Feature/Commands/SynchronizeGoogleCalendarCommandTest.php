<?php

use App\Console\Commands\SynchronizeGoogleCalendarCommand;
use App\Models\Connector;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize;
use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Pest\Laravel\artisan;
use function Tests\Helpers\calendarFactory;

uses()->group('commands');

it('starts synchronization of enabled google calendar connectors', function (): void {
    Queue::fake();

    // Create an enabled calendar with an enabled Google Calendar connector
    $enabledConnector = Connector::factory()
        ->for(Provider::googleCalendar())
        ->create();
    calendarFactory(connector: $enabledConnector)->create();

    // Create a calendar with a disabled connector (should not be synced)
    $disabledConnector = Connector::factory()
        ->for(Provider::googleCalendar())
        ->disabled()
        ->create();
    calendarFactory(connector: $disabledConnector)->create();

    // Create a calendar with Microsoft Outlook connector (should not be synced)
    $outlookConnector = Connector::factory()
        ->for(Provider::microsoftOutlook())
        ->create();
    calendarFactory(connector: $outlookConnector)->create();

    artisan(SynchronizeGoogleCalendarCommand::class)
        ->assertSuccessful();

    QueueableActionFake::assertPushedTimes(Synchronize::class);
});
