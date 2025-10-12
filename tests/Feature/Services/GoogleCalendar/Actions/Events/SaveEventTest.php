<?php

use App\Models\Calendar;
use App\Models\Event;
use App\Services\GoogleCalendar\Actions\Events\SaveEvent;
use App\Services\GoogleCalendar\Data\GoogleEventData;
use App\Services\GoogleCalendar\Enums\Status;

it('creates an event', function (): void {
    $calendar = Calendar::factory()->create();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'summary' => 'Event',
        'status' => Status::CONFIRMED,
        'startDateTime' => now()->toDateTimeString(),
        'startDate' => null,
        'endDateTime' => now()->addMinute()->toDateTimeString(),
        'endDate' => null,
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($googleEventData, $calendar);

    expect(Event::sole())
        ->calendar_id->toBe($calendar->id)
        ->external_id->toBe($googleEventData->id)
        ->title->toBe($googleEventData->summary)
        ->all_day->toBe(false)
        ->starts_at->toBe($googleEventData->startDateTime)
        ->ends_at->toBe($googleEventData->endDateTime)
        ->description->toBe($googleEventData->description);
});

it('updates an event', function (): void {
    $calendar = Calendar::factory()->create();

    $oldEvent = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => 'ev',
            'title' => 'Old Event',
            'all_day' => false,
            'starts_at' => '1946-01-01 12:34:56',
            'ends_at' => '1946-01-01 16:34:52',
            'description' => 'an old event',
        ]);

    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'summary' => 'Event',
        'status' => Status::CONFIRMED,
        'startDateTime' => null,
        'startDate' => now()->toDateString(),
        'endDateTime' => null,
        'endDate' => now()->addDays(2)->toDateString(),
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($googleEventData, $calendar);

    expect($oldEvent->refresh())
        ->calendar_id->toBe($calendar->id)
        ->external_id->toBe($googleEventData->id)
        ->title->toBe($googleEventData->summary)
        ->all_day->toBe(true)
        ->starts_at->toBe($googleEventData->startDate)
        ->ends_at->toBe($googleEventData->endDate)
        ->description->toBe($googleEventData->description);
});

it('does not save a cancelled event', function (): void {
    $calendar = Calendar::factory()->create();
    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'summary' => 'Event',
        'status' => Status::CANCELLED,
        'startDateTime' => now()->toDateTimeString(),
        'startDate' => null,
        'endDateTime' => now()->addMinute()->toDateTimeString(),
        'endDate' => null,
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($googleEventData, $calendar);

    expect(Event::count())->toBe(0);
});

it('deletes an existing cancelled event', function (): void {
    $calendar = Calendar::factory()->create();

    $oldEvent = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => 'ev',
            'title' => 'Old Event',
            'all_day' => false,
            'starts_at' => '1946-01-01 12:34:56',
            'ends_at' => '1946-01-01 16:34:52',
            'description' => 'an old event',
        ]);

    $googleEventData = GoogleEventData::from([
        'id' => 'ev',
        'summary' => 'Event',
        'status' => Status::CANCELLED,
        'startDateTime' => null,
        'startDate' => now()->toDateString(),
        'endDateTime' => null,
        'endDate' => now()->addDays(2)->toDateString(),
        'description' => 'an event',
    ]);

    app(SaveEvent::class)->execute($googleEventData, $calendar);

    expect($oldEvent->refresh())
        ->deleted_at->not()->toBeNull();
});
