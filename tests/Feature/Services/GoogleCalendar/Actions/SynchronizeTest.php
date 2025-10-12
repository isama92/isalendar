<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Synchronize;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Spatie\QueueableAction\ActionJob;

uses()->group('gcalendar', 'gcalendar.sync');

it('dispatches one job per enabled calendar in a single batch', function (): void {
    Bus::fake();

    $connector = Connector::factory()->create();

    // 3 enabled, 2 disabled calendars
    $enabledCalendars = Calendar::factory()->count(3)->create();
    $disabledCalendars = Calendar::factory()->count(2)->disabled()->create();

    $connector->calendars()->syncWithoutDetaching($enabledCalendars);
    $connector->calendars()->syncWithoutDetaching($disabledCalendars);

    app(Synchronize::class)->execute($connector);

    Bus::assertBatched(function (PendingBatch $batch) use ($connector, $enabledCalendars): true {
        expect($batch->name)->toBe(sprintf('Synchronize Google Calendar events (%s)', $connector->id))
            ->and(count($batch->jobs))->toBe($enabledCalendars->count());

        collect($batch->jobs)->each(function ($job): void {
            expect($job)->toBeInstanceOf(ActionJob::class);
        });

        return true;
    });
});

it('does not dispatch an empty batch when there are no enabled calendars', function (): void {
    Bus::fake();

    $connector = Connector::factory()->create();
    $calendars = Calendar::factory()->count(2)->disabled()->create();

    $connector->calendars()->sync($calendars);

    app(Synchronize::class)->execute($connector);

    Bus::assertNothingBatched();
});
