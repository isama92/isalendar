<?php

namespace App\Services\GoogleCalendar\Actions\Events;

use App\Data\EventData;
use App\Models\Calendar;
use App\Models\Event;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use App\Services\GoogleCalendar\Enums\Status;

class SaveEvent
{
    public function execute(GoogleEventData $googleEventData, Calendar $calendar): void
    {
        $event = $calendar->events()
            ->where('calendar_id', $calendar->id)
            ->where('external_id', $googleEventData->id)
            ->first();

        if ($googleEventData->status === Status::CANCELLED) {
            $event?->delete();

            return;
        }

        if ($event instanceof Event) {
            $event->update(
                EventData::from($googleEventData)->all()
            );

            return;
        }

        $calendar->events()->create(
            EventData::from($googleEventData)->all()
        );
    }
}
