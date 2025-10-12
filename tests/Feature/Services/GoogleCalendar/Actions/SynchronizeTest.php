<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Synchronize;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

uses()->group('gcalendar', 'gcalendar.sync');

it('dispatches import and export batches for enabled calendars', function (): void {
    $connector = Connector::factory()->create();

    // 3 enabled, 2 disabled calendars
    $enabledCalendars = Calendar::factory()->count(3)->create();
    $disabledCalendars = Calendar::factory()->count(2)->disabled()->create();

    $connector->calendars()->syncWithoutDetaching($enabledCalendars);
    $connector->calendars()->syncWithoutDetaching($disabledCalendars);

    $batchCount = 0;

    Bus::shouldReceive('batch')
        ->twice()
        ->andReturnUsing(function ($jobs) use (&$batchCount) {
            $batchCount++;
            $batch = Mockery::mock(PendingBatch::class);
            $batch->shouldReceive('name')->andReturnSelf();

            return $batch;
        });

    $pendingChain = Mockery::mock();
    $pendingChain->shouldReceive('dispatch')->once()->andReturnUndefined();

    Bus::shouldReceive('chain')
        ->once()
        ->with(Mockery::on(fn ($arg): bool => is_array($arg) && count($arg) === 2))
        ->andReturn($pendingChain);

    app(Synchronize::class)->execute($connector);

    expect($batchCount)->toBe(2);
});

it('does not dispatch an empty batch when there are no enabled calendars', function (): void {
    Bus::fake();

    $connector = Connector::factory()->create();
    $calendars = Calendar::factory()->count(2)->disabled()->create();

    $connector->calendars()->sync($calendars);

    app(Synchronize::class)->execute($connector);

    Bus::assertNothingBatched();
});
