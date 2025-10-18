<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\Calendar\GoogleCalendar\Actions\Events\FetchEvents;
use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use App\Services\Calendar\GoogleCalendar\Enums\Status;
use Google\Client as GoogleClient;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\Events;
use Illuminate\Support\Collection;

use function Pest\Laravel\partialMock;

uses()->group('gcalendar', 'gcalendar.events');

it('fetches events from Google and returns GoogleEventData', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();

    $connector->calendars()->attach($calendar);

    $ge1 = new GoogleEvent;
    $ge1Start = new EventDateTime;
    $ge1Start->setDateTime(now()->toRfc3339String());
    $ge1Start->setTimeZone('Europe/Amsterdam');
    $ge1End = new EventDateTime;
    $ge1End->setDateTime(now()->addHour()->toRfc3339String());
    $ge1End->setTimeZone('Europe/Amsterdam');
    $ge1->setId('ev_1');
    $ge1->setSummary('Event 1');
    $ge1->setStatus(Status::CONFIRMED->value);
    $ge1->setStart($ge1Start);
    $ge1->setEnd($ge1End);
    $ge1->setDescription('an event');

    $ge2 = new GoogleEvent;
    $ge2Start = new EventDateTime;
    $ge2Start->setDate(now()->toDateString());
    $ge2End = new EventDateTime;
    $ge2End->setDate(now()->addDay()->toDateString());
    $ge2->setId('ev_2');
    $ge2->setSummary('Event 2');
    $ge2->setStatus(Status::TENTATIVE->value);
    $ge2->setStart($ge2Start);
    $ge2->setEnd($ge2End);
    $ge2->setDescription('another event');

    $list = new Events;
    $list->setItems([$ge1, $ge2]);

    $googleClientMock = partialMock(GoogleClient::class);
    $googleClientMock->shouldReceive('getUniverseDomain')
        ->andReturn('googleapis.com');
    $googleClientMock->shouldReceive('execute')
        ->once()
        ->andReturn($list);

    partialMock(GetAuthClient::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($googleClientMock);

    $result = app(FetchEvents::class)->execute(
        $connector,
        $calendar,
        now()->toImmutable(),
        now()->toImmutable()
    );

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($result->get(0))
        ->toBeInstanceOf(GoogleEventData::class)
        ->id->toBe($ge1->getId())
        ->summary->toBe($ge1->getSummary())
        ->status->toBe(Status::CONFIRMED)
        ->timezone->toBe('Europe/Amsterdam')
        ->startDateTime->toBe($ge1->getStart()->getDateTime())
        ->startDate->toBeNull()
        ->endDateTime->toBe($ge1->getEnd()->getDateTime())
        ->endDate->toBeNull()
        ->description->toBe($ge1->getDescription())
        ->and($result->get(1))
        ->toBeInstanceOf(GoogleEventData::class)
        ->id->toBe($ge2->getId())
        ->summary->toBe($ge2->getSummary())
        ->status->toBe(Status::TENTATIVE)
        ->timezone->toBeNull()
        ->startDateTime->toBeNull()
        ->startDate->toBe($ge2->getStart()->getDate())
        ->endDateTime->toBeNull()
        ->endDate->toBe($ge2->getEnd()->getDate())
        ->description->toBe($ge2->getDescription());
});

it('returns an empty collection when Google returns no event items', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();

    $connector->calendars()->attach($calendar);

    $list = new Events;
    $list->setItems([]);

    $googleClientMock = partialMock(GoogleClient::class);
    $googleClientMock->shouldReceive('getUniverseDomain')
        ->andReturn('googleapis.com');
    $googleClientMock->shouldReceive('execute')
        ->once()
        ->andReturn($list);

    partialMock(GetAuthClient::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($googleClientMock);

    $result = app(FetchEvents::class)->execute(
        $connector,
        $calendar,
        now()->toImmutable(),
        now()->toImmutable()
    );

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toBeEmpty();
});
