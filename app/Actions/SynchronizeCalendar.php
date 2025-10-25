<?php

namespace App\Actions;

use App\Enums\Service;
use App\Models\Calendar;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;

class SynchronizeCalendar
{
    public function execute(Calendar $calendar): void
    {
        $synchronizeAction = match (Service::fromId($calendar->connectors()->first()->provider_id)) {
            Service::GOOGLE_CALENDAR => GoogleCalendarSynchronize::class,
            Service::MICROSOFT_OUTLOOK => null,
        };

        if (is_null($synchronizeAction)) {
            return;
        }

        app($synchronizeAction)->onQueue()->execute($calendar);
    }
}
