<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Data\EventData;
use App\Models\Calendar;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use App\Services\Calendar\GoogleCalendar\Enums\Status;

class SaveEvent
{
    public function execute(GoogleEventData $googleEventData, Calendar $calendar): void
    {
        /** @phpstan-ignore method.notFound */
        $event = $calendar->events()
            ->withMarkedForDeletion()
            ->where('calendar_id', $calendar->id)
            ->where('external_id', $googleEventData->id)
            ->first();

        if ($googleEventData->status === Status::CANCELLED) {
            $event?->delete();

            return;
        }

        $this->saveEvent($googleEventData, $calendar, $event);
    }

    private function saveEvent(GoogleEventData $googleEventData, Calendar $calendar, ?Event $event): void
    {
        if (! $event instanceof Event) {
            $calendar->events()->create(
                EventData::from($googleEventData)->all()
            );

            return;
        }

        if ($googleEventData->updatedAt->isBefore($event->updated_at)) {
            return;
        }

        $event->update(
            EventData::from($googleEventData)->all()
        );
    }
}
