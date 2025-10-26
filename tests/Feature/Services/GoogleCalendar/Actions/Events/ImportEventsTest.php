<?php

use App\Services\Calendar\GoogleCalendar\Actions\Events\FetchEvents;
use App\Services\Calendar\GoogleCalendar\Actions\Events\ImportEvents;
use App\Services\Calendar\GoogleCalendar\Actions\Events\SaveEvent;
use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use App\Services\Calendar\GoogleCalendar\Enums\Status;

use function Pest\Laravel\mock;
use function Pest\Laravel\partialMock;
use function Tests\Helpers\calendarFactory;

uses()->group('gcalendar', 'gcalendar.events');

it('calls FetchEvents and then calls SaveEvent for each event', function (): void {
    $fetchWindowStart = now()->toImmutable();
    $fetchWindowEnd = now()->addHour()->toImmutable();

    $calendar = calendarFactory()->create();
    $connector = $calendar->connectors->sole();

    $eventsDataCollection = collect([
        GoogleEventData::from([
            'id' => 'ev 1',
            'creatorId' => $connector->external_id,
            'status' => Status::CONFIRMED,
            'updatedAt' => now(),
            'summary' => 'Event 1',
            'timezone' => 'Europe/Amsterdam',
            'startDateTime' => 'start datetime',
            'startDate' => null,
            'endDateTime' => 'end datetime',
            'endDate' => null,
            'description' => 'an event',
        ]),
        GoogleEventData::from([
            'id' => 'ev 2',
            'creatorId' => $connector->external_id,
            'status' => Status::CANCELLED,
            'updatedAt' => now(),
            'summary' => 'Event 2',
            'timezone' => null,
            'startDateTime' => null,
            'startDate' => 'start date',
            'endDateTime' => null,
            'endDate' => 'end date',
            'description' => 'another event',
        ]),
    ]);

    mock(FetchEvents::class)
        ->shouldReceive('execute')
        ->once()
        ->with(Mockery::on(fn ($arg): bool => $arg->id === $connector->id), $calendar, $fetchWindowStart, $fetchWindowEnd)
        ->andReturn($eventsDataCollection);

    $saveEventMock = partialMock(SaveEvent::class);
    $saveEventMock->shouldReceive('execute')
        ->once()
        ->with(Mockery::on(fn ($arg): bool => $arg->id === $connector->id), $calendar, $eventsDataCollection->get(0))
        ->ordered('saveEventGroup')
        ->andReturnUndefined();
    $saveEventMock->shouldReceive('execute')
        ->once()
        ->with(Mockery::on(fn ($arg): bool => $arg->id === $connector->id), $calendar, $eventsDataCollection->get(1))
        ->ordered('saveEventGroup')
        ->andReturnUndefined();

    app(ImportEvents::class)->execute($calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});
