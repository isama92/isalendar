<?php

use App\Data\CalendarData;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\Calendar\GoogleCalendar\Actions\Calendars\FetchNormalizedCalendars;
use Google\Client as GoogleClient;
use Google\Service\Calendar\CalendarList;
use Google\Service\Calendar\CalendarListEntry as GoogleCalendar;
use Illuminate\Support\Collection;

use function Pest\Laravel\partialMock;
use function Tests\Helpers\connectorFactory;

uses()->group('gcalendar', 'gcalendar.calendars');

it('fetches calendars from Google and returns normalized CalendarData', function (): void {
    $connector = connectorFactory()->create();

    $gc1 = new GoogleCalendar;
    $gc1->setId('cal_1');
    $gc1->setSummary('Primary');
    $gc1->setDescription('Company wide calendar');

    $gc2 = new GoogleCalendar;
    $gc2->setId('cal_2');
    $gc2->setSummary('Holidays');
    $gc2->setDescription(null);

    $list = new CalendarList;
    $list->setItems([$gc1, $gc2]);

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

    $result = app(FetchNormalizedCalendars::class)->execute($connector);

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($result->get(0))
        ->toBeInstanceOf(CalendarData::class)
        ->externalId->toBe('cal_1')
        ->name->toBe('Primary')
        ->description->toBe('Company wide calendar')
        ->and($result->get(1))
        ->toBeInstanceOf(CalendarData::class)
        ->externalId->toBe('cal_2')
        ->name->toBe('Holidays')
        ->description->toBeNull();
});

it('returns an empty collection when Google returns no calendar items', function (): void {
    $connector = connectorFactory()->create();

    $list = new CalendarList;
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

    $result = app(FetchNormalizedCalendars::class)->execute($connector);

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toBeEmpty();
});
