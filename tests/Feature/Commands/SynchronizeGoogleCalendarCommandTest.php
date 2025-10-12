<?php

use App\Console\Commands\SynchronizeGoogleCalendarCommand;
use App\Models\Connector;
use App\Models\Provider;
use App\Services\GoogleCalendar\Actions\Synchronize;
use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Pest\Laravel\artisan;

it('starts synchronization of enabled google calendar connectors', function (): void {
    Queue::fake();

    $connectorGoogleEnabled = Connector::factory()
        ->for(Provider::googleCalendar())
        ->create();
    Connector::factory()
        ->for(Provider::googleCalendar())
        ->disabled()
        ->create();
    Connector::factory()
        ->for(Provider::microsoftOutlook())
        ->create();

    artisan(SynchronizeGoogleCalendarCommand::class)
        ->assertSuccessful();

    QueueableActionFake::assertPushedTimes(Synchronize::class);
});
