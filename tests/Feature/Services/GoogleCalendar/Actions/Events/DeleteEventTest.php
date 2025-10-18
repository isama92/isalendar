<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\Calendar\GoogleCalendar\Actions\Events\DeleteEvent;
use Google\Client as GoogleClient;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\partialMock;

uses()->group('gcalendar', 'gcalendar.events');

it('deletes event from Google Calendar and soft deletes locally', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create([
        'external_id' => 'calendar_external_id',
    ]);
    $connector->calendars()->attach($calendar);

    $event = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => 'event_to_delete',
            'title' => 'Event to Delete',
            'starts_at' => now()->toDateTimeString(),
            'ends_at' => now()->addHour()->toDateTimeString(),
        ]);

    $googleClientMock = partialMock(GoogleClient::class);
    $googleClientMock->shouldReceive('getUniverseDomain')
        ->andReturn('googleapis.com');

    $googleClientMock->shouldReceive('execute')
        ->once()
        ->andReturnNull();

    partialMock(GetAuthClient::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($googleClientMock);

    app(DeleteEvent::class)->execute($connector, $event);

    assertSoftDeleted('events', ['id' => $event->id]);
});

it('throws exception when event has no external_id', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();
    $connector->calendars()->attach($calendar);

    $event = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => null,
            'title' => 'Local Only Event',
        ]);

    expect(fn () => app(DeleteEvent::class)->execute($connector, $event))
        ->toThrow(RuntimeException::class, 'Cannot delete an API event without an external_id');
});
