<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Data\EventData;
use App\Models\Calendar;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\Events as GoogleEvents;
use Spatie\QueueableAction\QueueableAction;

readonly class FetchAndSaveEvents
{
    use QueueableAction;

    public function __construct(private GetAuthClient $getAuthClient) {}

    public function execute(Calendar $calendar): void
    {
        $client = $this->getAuthClient->execute($calendar);

        $events = $this->fetchEvents($client, $calendar);

        foreach ($events->getItems() as $googleEvent) {
            $this->saveEvent($googleEvent, $calendar);
        }
    }

    private function fetchEvents(GoogleClient $client, Calendar $calendar): GoogleEvents
    {
        $service = new GoogleCalendar($client);

        $timeMin = Carbon::now()->subWeek()->startOfDay()->toRfc3339String();
        $timeMax = Carbon::now()->addWeeks(2)->endOfDay()->toRfc3339String();

        $optParams = [
            'maxResults' => 2500,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
        ];

        return $service->events->listEvents($calendar->external_calendar_id, $optParams);
    }

    private function saveEvent(GoogleEvent $googleEvent, Calendar $calendar): void
    {
        $eventData = EventData::from($googleEvent);

        $calendar->user->events()
            ->updateOrCreate(
                [
                    'external_identifier' => $eventData->externalIdentifier,
                    'calendar_id' => $calendar->id,
                ],
                $eventData->all(),
            );
    }
}
