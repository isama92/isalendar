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
        /** @phpstan-ignore method.notFound */
        $event = $calendar->events()
            ->withMarkedForDeletion()
            ->where('calendar_id', $calendar->id)
            ->where('external_id', $googleEventData->id)
            ->first();

        Cache::lock(sprintf('calendar-sync-%s-%s', $calendar->id, $event?->id), 10)
            ->block(5, function () use ($googleEventData, $calendar, $connector, $event): void {
                if ($googleEventData->status === Status::CANCELLED) {
                    $event?->delete();

                    return;
                }

                $this->saveEvent($connector, $calendar, $googleEventData, $event);
            });
    }

    private function saveEvent(Connector $connector, Calendar $calendar, GoogleEventData $googleEventData, ?Event $event): void
    {
        // TODO: get right connector based on who created/updated event remotely

        if (! $event instanceof Event) {
            $calendar->events()->create([
                ...EventData::from($googleEventData)->all(),
                'created_by_connector_id' => $connector->id,
                'last_modified_by_connector_id' => $connector->id,
            ]);

            return;
        }

        if ($googleEventData->updatedAt->isBefore($event->updated_at)) {
            return;
        }

        $event->update([
            ...EventData::from($googleEventData)->all(),
            'last_modified_by_connector_id' => $connector->id,
        ]);
    }
}
