<?php

namespace App\Console\Commands;

use App\Actions\SynchronizeCalendar;
use App\Models\Calendar;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class SynchronizeCalendarCommand extends Command implements PromptsForMissingInput
{
    use ConfirmableTrait;

    protected $signature = 'app:synchronize:calendar
                            {calendar : The Calendar ID}
                            {--force : Force the operation without interaction}';

    protected $description = 'Run synchronization for a specific calendar';

    public function handle(SynchronizeCalendar $synchronizeCalendar): int
    {
        $calendar = Calendar::with(['connectors.user', 'connectors.provider', 'events'])
            ->find($this->argument('calendar'));

        if (! $calendar instanceof Calendar) {
            $this->error('Calendar does not exist');

            return self::FAILURE;
        }

        if (! $calendar->enabled && ! $this
            ->confirm('This calendar is disabled. Are you sure you want to synchronize it?')) {
            return self::SUCCESS;
        }

        $this->info('<comment>Calendar\'s information:</comment>');
        $this->info(sprintf('Calendar name: %s', $calendar->name));
        $this->info(sprintf('Number of connectors linked to this calendar: %d', $calendar->connectors->count()));
        $this->info(sprintf('Number of events: %d', $calendar->events->count()));

        if (! $this->confirmToProceed('Confirmation is required', true)) {
            return self::SUCCESS;
        }

        $synchronizeCalendar->execute($calendar);

        return self::SUCCESS;
    }
}
