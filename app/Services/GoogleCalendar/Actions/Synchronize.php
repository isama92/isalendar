<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Models\Calendar;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Events\PullEvents;
use Illuminate\Support\Facades\Bus;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\QueueableAction;

class Synchronize
{
    use QueueableAction;

    public function execute(Connector $connector): void
    {
        $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
        $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

        Bus::batch(
            $connector->calendars()
                ->enabled()
                ->lazyById()
                ->map(fn (Calendar $calendar): ActionJob => new ActionJob(PullEvents::class, [
                    $connector,
                    $calendar,
                    $fetchWindowStart,
                    $fetchWindowEnd,
                ]))
        )
            ->name(sprintf('Synchronize Google Calendar events (%s)', $connector->id))
            ->dispatch();
    }
}
