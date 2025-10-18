<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\Calendar\GoogleCalendar\Enums\Status;
use Carbon\Carbon;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\DB;
use Spatie\QueueableAction\QueueableAction;

class PushEvent
{
    use QueueableAction;

    public function __construct(private readonly GetAuthClient $getAuthClient) {}

    public function execute(Connector $connector, Event $event): void
    {
        $googleClient = $this->getAuthClient->execute($connector);

        DB::transaction(function () use ($googleClient, $event): void {
            $savedEvent = $this->pushEvent(
                new CalendarService($googleClient),
                $event,
                $this->getGoogleEvent($event),
            );

            $event->update(['external_id' => $savedEvent->getId()]);
        });
    }

    private function getGoogleEvent(Event $event): GoogleEvent
    {
        $googleEvent = new GoogleEvent;

        if (! is_null($event->external_id)) {
            $googleEvent->setId($event->external_id);
        }

        $googleEvent->setSummary($event->title);
        $googleEvent->setStatus(Status::CONFIRMED->value);
        $googleEvent->setStart($this->setEventDateTime($event->starts_at, $event->all_day, $event->timezone));
        $googleEvent->setEnd($this->setEventDateTime($event->ends_at, $event->all_day, $event->timezone));
        $googleEvent->setDescription($event->description);

        return $googleEvent;
    }

    private function setEventDateTime(Carbon $datetime, bool $allDay, ?string $timezone = null): EventDateTime
    {
        $eventDateTime = new EventDateTime;

        if ($allDay) {
            $eventDateTime->setDate($datetime->toDateString());

            return $eventDateTime;
        }

        $eventDateTime->setDateTime($datetime->toRfc3339String());
        $eventDateTime->setTimeZone($timezone);

        return $eventDateTime;
    }

    private function pushEvent(CalendarService $service, Event $event, GoogleEvent $googleEvent): GoogleEvent
    {
        $options = ['sendUpdates' => 'none', 'conferenceDataVersion' => 0];

        if (! is_null($event->external_id)) {
            return $service->events->patch(
                $event->calendar->external_id,
                $event->external_id,
                $googleEvent,
                $options,
            );
        }

        return $service->events->insert(
            $event->calendar->external_id,
            $googleEvent,
            $options,
        );
    }
}
