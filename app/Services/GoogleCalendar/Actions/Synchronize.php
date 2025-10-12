<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Jobs\ActionJob;
use App\Models\Calendar;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Events\ExportEvents;
use App\Services\GoogleCalendar\Actions\Events\ImportEvents;
use Illuminate\Support\Facades\Bus;
use Spatie\QueueableAction\QueueableAction;

class Synchronize
{
    use QueueableAction;

    public function execute(Connector $connector): void
    {
        $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
        $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

        if ($connector->calendars()->enabled()->doesntExist()) {
            return;
        }

        $enabledCalendars = $connector->calendars()
            ->enabled()
            ->get();

        Bus::chain([
            Bus::batch(
                $enabledCalendars
                    ->map(fn (Calendar $calendar): ActionJob => new ActionJob(ImportEvents::class, [
                        $connector,
                        $calendar,
                        $fetchWindowStart,
                        $fetchWindowEnd,
                    ]))
            )->name(sprintf('Import Google Calendar events (%s)', $connector->id)),
            Bus::batch(
                $enabledCalendars
                    ->map(fn (Calendar $calendar): ActionJob => new ActionJob(ExportEvents::class, [
                        $connector,
                        $calendar,
                        $fetchWindowStart,
                        $fetchWindowEnd,
                    ]))
            )->name(sprintf('Export Google Calendar events (%s)', $connector->id)),
        ])->dispatch();
    }
}
