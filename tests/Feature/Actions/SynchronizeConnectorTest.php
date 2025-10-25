<?php

use App\Actions\SynchronizeConnector;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;
use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('actions');

it('synchronizes Google Calendar connector', function (): void {
    Queue::fake();

    // Create connector with 2 enabled calendars
    $connector = connectorFactory()->create();
    calendarFactory(connector: $connector)->create();
    calendarFactory(connector: $connector)->create();

    app(SynchronizeConnector::class)->execute($connector);

    // Should push Synchronize action twice (once per calendar)
    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 2);
});

it('does not synchronize Microsoft Outlook connector', function (): void {
    Queue::fake();

    $connector = connectorFactory()->for(Provider::microsoftOutlook())->create();

    app(SynchronizeConnector::class)->execute($connector);

    Queue::assertNothingPushed();
});
