<?php

namespace App\Actions;

use App\Enums\Service;
use App\Models\Connector;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;

class SynchronizeConnector
{
    public function execute(Connector $connector): void
    {
        $synchronizeAction = match (Service::fromId($connector->provider_id)) {
            Service::GOOGLE_CALENDAR => GoogleCalendarSynchronize::class,
            Service::MICROSOFT_OUTLOOK => null,
        };

        if (is_null($synchronizeAction)) {
            return;
        }

        $connector->calendars()
            ->enabled()
            ->each(fn ($calendar) => app($synchronizeAction)
                ->onQueue()
                ->execute($calendar));
    }
}
