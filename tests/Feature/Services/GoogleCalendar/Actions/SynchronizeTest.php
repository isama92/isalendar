<?php

use App\Services\Calendar\GoogleCalendar\Actions\Synchronize;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

use function Tests\Helpers\calendarFactory;

uses()->group('gcalendar', 'gcalendar.sync');

it('dispatches import and export batches for enabled calendars and sets synchronized_at', function (): void {
    // 3 enabled, 2 disabled calendars
    $connector = calendarFactory(3)->create()
        ->first()->connectors->sole();

    calendarFactory(2)->disabled()->create();

    $batchCount = 0;
    $finallyCallback = null;

    Bus::shouldReceive('batch')
        ->twice()
        ->andReturnUsing(function ($jobs) use (&$batchCount, &$finallyCallback) {
            $batchCount++;
            $batch = Mockery::mock(PendingBatch::class);
            $batch->shouldReceive('name')->andReturnSelf();

            // Only the second batch (export) should have finally
            if ($batchCount === 2) {
                $batch->shouldReceive('finally')
                    ->once()
                    ->andReturnUsing(function ($callback) use ($batch, &$finallyCallback) {
                        $finallyCallback = $callback;

                        return $batch;
                    });
            }

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

    // Execute the finally callback to simulate batch completion
    expect($finallyCallback)->toBeCallable();
    $finallyCallback();

    expect($connector->refresh()->synchronized_at)->not->toBeNull();
});

it('does not dispatch an empty batch when there are no enabled calendars', function (): void {
    Bus::fake();

    $connector = calendarFactory(2)->create()
        ->first()->connectors->sole();

    app(Synchronize::class)->execute($connector);

    Bus::assertNothingBatched();
});
