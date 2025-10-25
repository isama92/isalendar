<?php

namespace App\Console\Commands;

use App\Builders\ConnectorBuilder;
use App\Models\Calendar;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize;
use Illuminate\Console\Command;

class SynchronizeGoogleCalendarCommand extends Command
{
    protected $signature = 'app:synchronize:google';

    protected $description = 'Synchronize all google calendars';

    public function handle(Synchronize $synchronize): int
    {
        Calendar::query()
            ->enabled()
            /** @phpstan-ignore argument.type */
            ->whereHas('connectors', fn (ConnectorBuilder $query) => $query
                ->whereBelongsTo(Provider::googleCalendar())
                ->enabled())
            ->lazyById()
            ->each(fn (Calendar $calendar) => $synchronize
                ->onQueue()
                ->execute($calendar));

        return self::SUCCESS;
    }
}
