<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Actions\GetEventConnector;
use App\Exceptions\CouldNotGetEventConnectorException;
use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use Google\Service\Calendar as CalendarService;
use RuntimeException;
use Spatie\QueueableAction\QueueableAction;

class DeleteEvent
{
    use QueueableAction;

    public function __construct(
        private readonly GetEventConnector $getEventConnector,
        private readonly GetAuthClient $getAuthClient,
    ) {}

    public function execute(Event $event): void
    {
        throw_if(is_null($event->external_id), new RuntimeException('Cannot delete an API event without an external_id'));

        $connector = $this->getEventConnector->execute($event);
        throw_unless($connector instanceof Connector, new CouldNotGetEventConnectorException($event));

        $googleClient = $this->getAuthClient->execute($connector);
        $service = new CalendarService($googleClient);
        $service->events->delete($event->calendar->external_id, $event->external_id);

        $event->delete();
    }
}
