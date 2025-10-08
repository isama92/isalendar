<?php

namespace App\Console\Commands;

use App\Models\Connector;
use App\Models\Provider;
use App\Services\GoogleCalendar\Actions\Synchronize;
use Illuminate\Console\Command;

class SynchronizeGoogleCalendarCommand extends Command
{
    protected $signature = 'app:synchronize:google';

    protected $description = 'Synchronize all google calendars';

    public function handle(Synchronize $synchronize): int
    {
        Connector::query()
            ->whereBelongsTo(Provider::googleCalendar())
            ->enabled()
            ->lazyById()
            ->each(fn (Connector $connector) => $synchronize
                ->onQueue()
                ->execute($connector));

        return self::SUCCESS;
    }
}
