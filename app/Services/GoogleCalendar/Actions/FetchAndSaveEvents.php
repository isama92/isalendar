<?php

namespace App\Services\GoogleCalendar\Actions;

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
        $startObj = $googleEvent->getStart();
        $endObj = $googleEvent->getEnd();

        /** @var null|string $startDateTime */
        $startDateTime = $startObj->getDateTime();

        /** @var null|string $endDateTime */
        $endDateTime = $endObj->getDateTime();

        $calendar->user->events()->updateOrCreate(
            [
                'external_identifier' => $googleEvent->id,
                'calendar_id' => $calendar->id,
            ],
            [
                'title' => $googleEvent->getSummary(),
                'description' => $googleEvent->getDescription(),
                'all_day' => $startDateTime === null,
                'starts_at' => Carbon::parse($startDateTime ?? $startObj->getDate()),
                'ends_at' => Carbon::parse($endDateTime ?? $endObj->getDate()),
            ]
        );
    }
}
