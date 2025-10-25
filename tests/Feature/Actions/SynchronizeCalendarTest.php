<?php

use App\Actions\SynchronizeCalendar;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;
use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('actions');

it('synchronizes a Google Calendar calendar', function (): void {
    Queue::fake();

    $calendar = calendarFactory()->create();

    app(SynchronizeCalendar::class)->execute($calendar);

    QueueableActionFake::assertPushed(GoogleCalendarSynchronize::class);
});

it('does not synchronize a Microsoft Outlook calendar', function (): void {
    Queue::fake();

    $connector = connectorFactory()->for(Provider::microsoftOutlook())->create();
    $calendar = calendarFactory(connector: $connector)->create();

    app(SynchronizeCalendar::class)->execute($calendar);

    Queue::assertNothingPushed();
});
