<?php

namespace App\Console\Commands;

use App\Builders\ConnectorBuilder;
use App\Models\Calendar;
use App\Models\Provider;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize;
use Illuminate\Console\Command;
use Override;

class SynchronizeGoogleCalendarCommand extends Command
{
    #[Override]
    protected $signature = 'app:synchronize:google';

    #[Override]
    protected $description = 'Synchronize all google calendars';

    public function handle(Synchronize $synchronize): int
    {
        Calendar::query()
            ->enabled()
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
