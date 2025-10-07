<?php

namespace App\Services\GoogleCalendar\Actions\Events;

use App\Data\EventData;
use App\Models\Calendar;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use App\Services\GoogleCalendar\Enums\Status;

class SaveEvent
{
    public function execute(GoogleEventData $googleEventData, Calendar $calendar): void
    {
        $event = $calendar->user->events()
            ->where('calendar_id', $calendar->id)
            ->where('external_identifier', $googleEventData->id)
            ->first();

        if ($googleEventData->status === Status::CANCELLED) {
            $event?->delete();

            return;
        }

        $event?->updateOrCreate(
            [
                'user_id' => $calendar->user_id,
                'calendar_id' => $calendar->id,
                'external_identifier' => $googleEventData->id,
            ],
            EventData::from($googleEventData)->all(),
        )
            ->touch();
    }
}
