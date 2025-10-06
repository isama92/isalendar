<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Data\EventData;
use App\Models\Calendar;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as CalendarService;
use Google\Service\Calendar\Event as GoogleEvent;
use Illuminate\Support\Collection;
use Spatie\QueueableAction\QueueableAction;

readonly class SynchronizeCalendarEvents
{
    use QueueableAction;

    public function __construct(private GetAuthClient $getAuthClient) {}

    public function execute(Calendar $calendar): void
    {
        $client = $this->getAuthClient->execute($calendar);

        $this->fetchEvents($client, $calendar)
            ->each(fn (GoogleEventData $googleEventData) => $this->saveEvent($googleEventData, $calendar));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Services\GoogleCalendar\Data\GoogleEventData>
     */
    private function fetchEvents(GoogleClient $client, Calendar $calendar): Collection
    {
        $service = new CalendarService($client);

        $timeMin = Carbon::now()->subWeek()->startOfDay()->toRfc3339String();
        $timeMax = Carbon::now()->addWeeks(2)->endOfDay()->toRfc3339String();

        $optParams = [
            'maxResults' => 2500,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
        ];

        /** @var \Google\Service\Calendar\Events $eventList */
        $eventList = $service->events
            ->listEvents($calendar->external_calendar_id, $optParams);

        return collect($eventList->getItems())
            ->map(fn (GoogleEvent $googleEvent): GoogleEventData => GoogleEventData::from($googleEvent));
    }

    private function saveEvent(GoogleEventData $googleEventData, Calendar $calendar): void
    {
        $calendar->user->events()
            ->updateOrCreate(
                [
                    'external_identifier' => $googleEventData->id,
                    'calendar_id' => $calendar->id,
                ],
                EventData::from($googleEventData)->all(),
            );
    }
}
