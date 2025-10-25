<?php

use App\Services\Calendar\GoogleCalendar\Actions\Synchronize;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

use function Tests\Helpers\calendarFactory;

uses()->group('gcalendar', 'gcalendar.sync');

it('dispatches import and export jobs for calendar and sets synchronized_at', function (): void {
    $calendar = calendarFactory()->create();

    $finallyCallback = null;

    $pendingBatch = Mockery::mock(PendingBatch::class);
    $pendingBatch->shouldReceive('name')
        ->once()
        ->with(Mockery::pattern('/^Synchronize Calendar/'))
        ->andReturnSelf();
    $pendingBatch->shouldReceive('finally')
        ->once()
        ->andReturnUsing(function ($callback) use ($pendingBatch, &$finallyCallback) {
            $finallyCallback = $callback;

            return $pendingBatch;
        });
    $pendingBatch->shouldReceive('dispatch')->once()->andReturnUndefined();

    Bus::shouldReceive('batch')
        ->once()
        ->with(Mockery::on(fn ($arg): bool =>
            // Verify it's an array containing one nested array with 2 jobs
            is_array($arg) &&
            count($arg) === 1 &&
            is_array($arg[0]) &&
            count($arg[0]) === 2))
        ->andReturn($pendingBatch);

    app(Synchronize::class)->execute($calendar);

    // Execute the finally callback to simulate batch completion
    expect($finallyCallback)->toBeCallable();
    $finallyCallback();

    expect($calendar->refresh()->synchronized_at)->not->toBeNull();
});

it('does not dispatch jobs when calendar is disabled', function (): void {
    Bus::fake();

    $calendar = calendarFactory()->disabled()->create();

    app(Synchronize::class)->execute($calendar);

    Bus::assertNothingDispatched();
});

it('does not dispatch jobs when calendar has no enabled connectors', function (): void {
    Bus::fake();

    $calendar = calendarFactory()->create();
    $calendar->connectors()->update(['enabled' => false]);

    app(Synchronize::class)->execute($calendar);

    Bus::assertNothingDispatched();
});
