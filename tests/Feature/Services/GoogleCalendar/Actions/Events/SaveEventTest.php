<?php

use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Events\SaveEvent;
use App\Services\Calendar\GoogleCalendar\Data\GoogleEventData;
use App\Services\Calendar\GoogleCalendar\Enums\Status;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\eventFactory;

uses()->group('gcalendar', 'gcalendar.events');

it('creates an event', function (): void {
    $calendar = calendarFactory()->create();
    $connector = $calendar->connectors()->first();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'creatorId' => $connector->external_id,
        'status' => Status::CONFIRMED,
        'updatedAt' => now(),
        'summary' => 'Event',
        'timezone' => 'Europe/Amsterdam',
        'startDateTime' => now()->toDateTimeString(),
        'startDate' => null,
        'endDateTime' => now()->addMinute()->toDateTimeString(),
        'endDate' => null,
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($connector, $calendar, $googleEventData);

    expect(Event::sole())
        ->calendar_id->toBe($calendar->id)
        ->external_id->toBe($googleEventData->id)
        ->title->toBe($googleEventData->summary)
        ->all_day->toBe(false)
        ->timezone->toBe('Europe/Amsterdam')
        ->starts_at->toBe($googleEventData->startDateTime)
        ->ends_at->toBe($googleEventData->endDateTime)
        ->description->toBe($googleEventData->description)
        ->created_by_connector_id->toBe($connector->id)
        ->last_modified_by_connector_id->toBe($connector->id);
});

it('updates an event when remote updatedAt is after local updated_at', function (): void {
    $oldEvent = eventFactory()->create([
        'external_id' => 'ev',
        'title' => 'Old Event',
        'all_day' => false,
        'timezone' => 'Europe/Amsterdam',
        'starts_at' => '1946-01-01 12:34:56',
        'ends_at' => '1946-01-01 16:34:52',
        'description' => 'an old event',
        'updated_at' => now()->subHour(),
    ]);

    $connector = $oldEvent->calendar->connectors()->first();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'creatorId' => $connector->external_id,
        'status' => Status::CONFIRMED,
        'updatedAt' => now(),
        'summary' => 'Event',
        'timezone' => null,
        'startDateTime' => null,
        'startDate' => now()->toDateString(),
        'endDateTime' => null,
        'endDate' => now()->addDays(2)->toDateString(),
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($connector, $oldEvent->calendar, $googleEventData);

    expect($oldEvent->refresh())
        ->calendar_id->toBe($oldEvent->calendar->id)
        ->external_id->toBe($googleEventData->id)
        ->title->toBe($googleEventData->summary)
        ->all_day->toBe(true)
        ->timezone->toBeNull()
        ->starts_at->toBe($googleEventData->startDate)
        ->ends_at->toBe($googleEventData->endDate)
        ->description->toBe($googleEventData->description)
        ->last_modified_by_connector_id->toBe($connector->id);
});

it('does not update an event when remote updatedAt is before local updated_at', function (): void {
    $oldEvent = eventFactory()
        ->create([
            'external_id' => 'ev',
            'title' => 'Old Event',
            'all_day' => false,
            'timezone' => 'Europe/Amsterdam',
            'starts_at' => '1946-01-01 12:34:56',
            'ends_at' => '1946-01-01 16:34:52',
            'description' => 'an old event',
            'updated_at' => now(),
        ]);

    $connector = $oldEvent->calendar->connectors()->first();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'creatorId' => $connector->external_id,
        'status' => Status::CONFIRMED,
        'updatedAt' => now()->subHour(),
        'summary' => 'New Event Title',
        'timezone' => 'America/New_York',
        'startDateTime' => null,
        'startDate' => now()->toDateString(),
        'endDateTime' => null,
        'endDate' => now()->addDays(2)->toDateString(),
        'description' => 'a new event description',
    ]);

    app(SaveEvent::class)->execute($connector, $oldEvent->calendar, $googleEventData);

    expect($oldEvent->refresh())
        ->calendar_id->toBe($oldEvent->calendar->id)
        ->external_id->toBe('ev')
        ->title->toBe('Old Event')
        ->all_day->toBe(false)
        ->timezone->toBe('Europe/Amsterdam')
        ->starts_at->toBe('1946-01-01 12:34:56')
        ->ends_at->toBe('1946-01-01 16:34:52')
        ->description->toBe('an old event');
});

it('does not save a cancelled event', function (): void {
    $calendar = calendarFactory()->create();
    $connector = $calendar->connectors()->first();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'creatorId' => $connector->external_id,
        'status' => Status::CANCELLED,
        'updatedAt' => now(),
        'summary' => 'Event',
        'timezone' => 'Europe/Amsterdam',
        'startDateTime' => now()->toDateTimeString(),
        'startDate' => null,
        'endDateTime' => now()->addMinute()->toDateTimeString(),
        'endDate' => null,
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($connector, $calendar, $googleEventData);

    expect(Event::count())->toBe(0);
});

it('deletes an existing cancelled event', function (): void {
    $oldEvent = eventFactory()
        ->create([
            'external_id' => 'ev',
            'title' => 'Old Event',
            'all_day' => false,
            'timezone' => 'Europe/Amsterdam',
            'starts_at' => '1946-01-01 12:34:56',
            'ends_at' => '1946-01-01 16:34:52',
            'description' => 'an old event',
        ]);

    $connector = $oldEvent->calendar->connectors()->first();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'creatorId' => $connector->external_id,
        'status' => Status::CANCELLED,
        'updatedAt' => now(),
        'summary' => null,
        'timezone' => null,
        'startDateTime' => null,
        'startDate' => now()->toDateString(),
        'endDateTime' => null,
        'endDate' => now()->addDays(2)->toDateString(),
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($connector, $oldEvent->calendar, $googleEventData);

    expect($oldEvent->refresh())
        ->deleted_at->not()->toBeNull();
});

it('sets created_by_connector_id to null when creator does not exist in database', function (): void {
    $calendar = calendarFactory()->create();
    $connector = $calendar->connectors()->first();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'creatorId' => 'external-user-id-not-in-db',
        'status' => Status::CONFIRMED,
        'updatedAt' => now(),
        'summary' => 'Event created by external user',
        'timezone' => 'Europe/Amsterdam',
        'startDateTime' => now()->toDateTimeString(),
        'startDate' => null,
        'endDateTime' => now()->addMinute()->toDateTimeString(),
        'endDate' => null,
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($connector, $calendar, $googleEventData);

    expect(Event::sole())
        ->created_by_connector_id->toBeNull()
        ->last_modified_by_connector_id->toBe($connector->id);
});
