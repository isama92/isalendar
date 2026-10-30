<?php

namespace App\Console\Commands;

use App\Models\Calendar;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Override;

class ForceDeleteCalendarCommand extends Command implements PromptsForMissingInput
{
    use ConfirmableTrait;

    #[Override]
    protected $signature = 'app:force-delete:calendar
                            {calendar : The Calendar ID}
                            {--force : Force the operation without interaction}';

    #[Override]
    protected $description = 'Force delete a calendar and all its related events';

    public function handle(): int
    {
        $calendar = Calendar::withTrashed()
            ->with(['connectors.user', 'connectors.provider', 'events'])
            ->find($this->argument('calendar'));

        if (! $calendar instanceof Calendar) {
            $this->error('Calendar does not exist');

            return self::FAILURE;
        }

        $this->warn('<comment>Calendar\'s information:</comment>');
        $this->info(sprintf('Calendar name: %s', $calendar->name));
        $this->info(sprintf('Number of connectors linked to this calendar: %d', $calendar->connectors->count()));
        $this->info(sprintf('Number of events: %d', $calendar->events()->withTrashed()->count()));
        $this->info(sprintf('Number of DELETED events: %d', $calendar->events()->onlyTrashed()->count()));

        if ($calendar->trashed()) {
            $this->warn('This calendar is already soft deleted.');
        }

        $this->warn('⚠️  This action will permanently delete the calendar and all its events.');

        if (! $this->confirmToProceed('Confirmation is required', true)) {
            return self::SUCCESS;
        }

        $calendar->connectors()->detach();
        $calendar->events()->forceDelete();
        $calendar->forceDelete();

        $this->info('Calendar force deleted successfully.');

        return self::SUCCESS;
    }
}
