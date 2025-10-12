<?php

use App\Actions\SynchronizeConnector;
use App\Enums\Service;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;
use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\Testing\QueueableActionFake;

uses()->group('actions');

it('synchronizes Google Calendar connector', function (): void {
    Queue::fake();

    $connector = Connector::factory()->create([
        'provider_id' => Service::GOOGLE_CALENDAR->id(),
    ]);

    app(SynchronizeConnector::class)->execute($connector);

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class);
});

it('does not synchronize Microsoft Outlook connector', function (): void {
    Queue::fake();

    $connector = Connector::factory()->create([
        'provider_id' => Service::MICROSOFT_OUTLOOK->id(),
    ]);

    app(SynchronizeConnector::class)->execute($connector);

    Queue::assertNothingPushed();
});
