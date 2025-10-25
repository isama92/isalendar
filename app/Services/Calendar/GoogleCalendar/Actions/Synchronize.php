<?php

namespace App\Services\Calendar\GoogleCalendar\Actions;

use App\Jobs\ActionJob;
use App\Models\Calendar;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Events\ExportEvents;
use App\Services\Calendar\GoogleCalendar\Actions\Events\ImportEvents;
use Illuminate\Support\Facades\Bus;
use Spatie\QueueableAction\QueueableAction;

class Synchronize
{
    use QueueableAction;

    public function execute(Calendar $calendar): void
    {
        if (! $calendar->enabled) {
            return;
        }

        $connector = $calendar->connectors()
            ->whereBelongsTo(Provider::googleCalendar())
            ->enabled()
            ->first();

        if (! $connector) {
            return;
        }

        $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
        $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

        Bus::batch([
            [
                new ActionJob(ImportEvents::class, [
                    $connector,
                    $calendar,
                    $fetchWindowStart,
                    $fetchWindowEnd,
                ]),
                new ActionJob(ExportEvents::class, [
                    $connector,
                    $calendar,
                    $fetchWindowStart,
                    $fetchWindowEnd,
                ]),
            ],
        ])
            ->name(sprintf('Synchronize Calendar (%s)', $calendar->id))
            ->finally(function () use ($connector): void {
                $connector->update(['synchronized_at' => now()]);
            })
            ->dispatch();
    }
}
