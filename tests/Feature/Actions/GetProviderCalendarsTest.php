<?php

use App\Actions\GetProviderCalendars;
use App\Data\CalendarData;
use App\Enums\Service;
use App\Models\Connector;
use App\Services\Calendar\GoogleCalendar\Actions\Calendars\FetchNormalizedCalendars;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Optional;

use function Pest\Laravel\partialMock;

uses()->group('actions');

it('fetches calendars for Google Calendar provider', function (): void {
    $connector = Connector::factory()->create([
        'provider_id' => Service::GOOGLE_CALENDAR->id(),
    ]);

    $expectedCalendars = collect([
        new CalendarData(
            externalId: 'cal_1',
            name: 'Primary Calendar',
            description: 'Main calendar'
        ),
        new CalendarData(
            externalId: 'cal_2',
            name: 'Work',
            description: Optional::create()
        ),
    ]);

    partialMock(FetchNormalizedCalendars::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($expectedCalendars);

    $result = app(GetProviderCalendars::class)->execute($connector);

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($result->get(0))
        ->toBeInstanceOf(CalendarData::class)
        ->externalId->toBe('cal_1')
        ->name->toBe('Primary Calendar')
        ->description->toBe('Main calendar')
        ->and($result->get(1))
        ->toBeInstanceOf(CalendarData::class)
        ->externalId->toBe('cal_2')
        ->name->toBe('Work');
});

it('returns empty collection for Microsoft Outlook provider', function (): void {
    $connector = Connector::factory()->create([
        'provider_id' => Service::MICROSOFT_OUTLOOK->id(),
    ]);

    $result = app(GetProviderCalendars::class)->execute($connector);

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toBeEmpty();
});
