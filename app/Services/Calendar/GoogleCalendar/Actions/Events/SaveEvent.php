<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Data\EventData;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use App\Services\Calendar\GoogleCalendar\Enums\Status;
use Illuminate\Support\Facades\Cache;

class SaveEvent
{
    public function execute(Connector $connector, Calendar $calendar, GoogleEventData $googleEventData): void
    {
        $event = $calendar->events()
            /** @phpstan-ignore method.notFound */
            ->withMarkedForDeletion()
            ->where('calendar_id', $calendar->id)
            ->where('external_id', $googleEventData->id)
            ->first();

        Cache::lock(sprintf('calendar-sync-%s-%s', $calendar->id, $event?->id), 10)
            ->block(5, function () use ($connector, $googleEventData, $calendar, $event): void {
                if ($googleEventData->status === Status::CANCELLED) {
                    $event?->delete();

                    return;
                }

                $this->saveEvent($connector, $calendar, $googleEventData, $event);
            });
    }

    private function saveEvent(Connector $connector, Calendar $calendar, GoogleEventData $googleEventData, ?Event $event): void
    {
        $creatorConnector = $googleEventData->creatorId
            ? $calendar->connectors()->where('external_id', $googleEventData->creatorId)->first()
            : null;

        if (! $event instanceof Event) {
            $calendar->events()->create([
                ...EventData::from($googleEventData)->all(),
                'created_by_connector_id' => ($creatorConnector ?? $connector)->id,
            ]);

            return;
        }

        if ($googleEventData->updatedAt->isBefore($event->updated_at)) {
            return;
        }

        $event->update(EventData::from($googleEventData)->all());
    }
}
