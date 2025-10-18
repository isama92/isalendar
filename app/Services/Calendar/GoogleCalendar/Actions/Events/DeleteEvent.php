<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use Google\Service\Calendar as CalendarService;
use RuntimeException;
use Spatie\QueueableAction\QueueableAction;

class DeleteEvent
{
    use QueueableAction;

    public function __construct(private readonly GetAuthClient $getAuthClient) {}

    public function execute(Connector $connector, Event $event): void
    {
        if (is_null($event->external_id)) {
            throw new RuntimeException('Cannot delete an API event without an external_id');
        }

        $googleClient = $this->getAuthClient->execute($connector);
        $service = new CalendarService($googleClient);
        $service->events->delete($event->calendar->external_id, $event->external_id);

        $event->delete();
    }
}
