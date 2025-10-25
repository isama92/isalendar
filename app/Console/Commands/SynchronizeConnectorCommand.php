<?php

namespace App\Console\Commands;

use App\Actions\SynchronizeCalendar;
use App\Models\Connector;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class SynchronizeConnectorCommand extends Command implements PromptsForMissingInput
{
    use ConfirmableTrait;

    protected $signature = 'app:synchronize:connector
                            {connector : The Connector ID}
                            {--force : Force the operation without interaction}';

    protected $description = 'Run synchronization a specific connector';

    public function handle(SynchronizeCalendar $synchronizeCalendar): int
    {
        $connector = Connector::with(['user', 'provider', 'calendars'])
            ->find($this->argument('connector'));

        if (! $connector instanceof Connector) {
            $this->error('Connector does not exist');

            return self::FAILURE;
        }

        if (! $connector->enabled && ! $this
            ->confirm('This connector is disabled. Are you sure you want to synchronize it?')) {
            return self::SUCCESS;
        }

        $this->info('<comment>Connector\'s information:</comment>');
        $this->info(sprintf('User full name: %s', $connector->user->full_name));
        $this->info(sprintf('Provider: %s', $connector->provider->name ?? 'None'));
        $this->info(sprintf('Number of calendars linked to this connector: %d', $connector->calendars->count()));

        if (! $this->confirmToProceed('Confirmation is required', true)) {
            return self::SUCCESS;
        }

        $connector->calendars()
            ->enabled()
            ->each(fn ($calendar) => $synchronizeCalendar
                ->execute($calendar));

        return self::SUCCESS;
    }
}
