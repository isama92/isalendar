<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Events\FetchEvents;
use App\Services\GoogleCalendar\Actions\Events\PullEvents;
use App\Services\GoogleCalendar\Actions\Events\SaveEvent;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use App\Services\GoogleCalendar\Enums\Status;

use function Pest\Laravel\partialMock;

it('calls FetchEvents and then calls SaveEvent for each event', function (): void {
    $fetchWindowStart = now()->toImmutable();
    $fetchWindowEnd = now()->addHour()->toImmutable();
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();

    $connector->calendars()->attach($calendar);

    $eventsDataCollection = collect([
        GoogleEventData::from([
            'id' => 'ev 1',
            'summary' => 'Event 1',
            'status' => Status::CONFIRMED,
            'timezone' => 'Europe/Amsterdam',
            'startDateTime' => 'start datetime',
            'startDate' => null,
            'endDateTime' => 'end datetime',
            'endDate' => null,
            'description' => 'an event',
        ]),
        GoogleEventData::from([
            'id' => 'ev 2',
            'summary' => 'Event 2',
            'status' => Status::CANCELLED,
            'timezone' => null,
            'startDateTime' => null,
            'startDate' => 'start date',
            'endDateTime' => null,
            'endDate' => 'end date',
            'description' => 'another event',
        ]),
    ]);

    partialMock(FetchEvents::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector, $calendar, $fetchWindowStart, $fetchWindowEnd)
        ->andReturn($eventsDataCollection);

    $saveEventMock = partialMock(SaveEvent::class);
    $saveEventMock->shouldReceive('execute')
        ->once()
        ->with($eventsDataCollection->get(0), $calendar)
        ->ordered('saveEventGroup')
        ->andReturnUndefined();
    $saveEventMock->shouldReceive('execute')
        ->once()
        ->with($eventsDataCollection->get(1), $calendar)
        ->ordered('saveEventGroup')
        ->andReturnUndefined();

    app(PullEvents::class)->execute($connector, $calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});
