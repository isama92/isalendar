<?php

use App\Actions\SynchronizeConnector;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;
use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Tests\Helpers\connectorFactory;

uses()->group('actions');

it('synchronizes Google Calendar connector', function (): void {
    Queue::fake();

    $connector = connectorFactory()->create();

    app(SynchronizeConnector::class)->execute($connector);

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class);
});

it('does not synchronize Microsoft Outlook connector', function (): void {
    Queue::fake();

    $connector = connectorFactory()->for(Provider::microsoftOutlook())->create();

    app(SynchronizeConnector::class)->execute($connector);

    Queue::assertNothingPushed();
});
