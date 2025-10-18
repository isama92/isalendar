<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\Calendar\GoogleCalendar\Actions\Events\PushEvent;
use Google\Client as GoogleClient;
use Google\Service\Calendar\Event as GoogleEvent;

use function Pest\Laravel\partialMock;

uses()->group('gcalendar', 'gcalendar.events');

it('creates a new event on Google Calendar when external_id is null', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();
    $connector->calendars()->attach($calendar);

    $event = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => null,
            'title' => 'New Event',
            'starts_at' => now()->toDateTimeString(),
            'ends_at' => now()->addHour()->toDateTimeString(),
            'all_day' => false,
            'timezone' => 'Europe/Amsterdam',
        ]);

    $googleClientMock = partialMock(GoogleClient::class);
    $googleClientMock->shouldReceive('getUniverseDomain')
        ->andReturn('googleapis.com');

    $createdGoogleEvent = new GoogleEvent;
    $createdGoogleEvent->setId('google_event_123');

    $googleClientMock->shouldReceive('execute')
        ->once()
        ->andReturn($createdGoogleEvent);

    partialMock(GetAuthClient::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($googleClientMock);

    app(PushEvent::class)->execute($connector, $event);

    expect($event->refresh()->external_id)->toBe('google_event_123');
});

it('updates an existing event on Google Calendar when external_id exists', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();
    $connector->calendars()->attach($calendar);

    $event = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => 'existing_google_id',
            'title' => 'Updated Event',
            'starts_at' => now()->toDateTimeString(),
            'ends_at' => now()->addHour()->toDateTimeString(),
            'all_day' => false,
            'timezone' => 'Europe/Amsterdam',
        ]);

    $googleClientMock = partialMock(GoogleClient::class);
    $googleClientMock->shouldReceive('getUniverseDomain')
        ->andReturn('googleapis.com');

    $updatedGoogleEvent = new GoogleEvent;
    $updatedGoogleEvent->setId('existing_google_id');

    $googleClientMock->shouldReceive('execute')
        ->once()
        ->andReturn($updatedGoogleEvent);

    partialMock(GetAuthClient::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($googleClientMock);

    app(PushEvent::class)->execute($connector, $event);

    expect($event->refresh()->external_id)->toBe('existing_google_id');
});

it('handles all-day events correctly', function (): void {
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();
    $connector->calendars()->attach($calendar);

    $event = Event::factory()
        ->for($calendar)
        ->create([
            'external_id' => null,
            'title' => 'All Day Event',
            'starts_at' => now()->startOfDay()->toDateTimeString(),
            'ends_at' => now()->endOfDay()->toDateTimeString(),
            'all_day' => true,
            'timezone' => null,
        ]);

    $googleClientMock = partialMock(GoogleClient::class);
    $googleClientMock->shouldReceive('getUniverseDomain')
        ->andReturn('googleapis.com');

    $createdGoogleEvent = new GoogleEvent;
    $createdGoogleEvent->setId('all_day_event_123');

    $googleClientMock->shouldReceive('execute')
        ->once()
        ->andReturn($createdGoogleEvent);

    partialMock(GetAuthClient::class)
        ->shouldReceive('execute')
        ->once()
        ->with($connector)
        ->andReturn($googleClientMock);

    app(PushEvent::class)->execute($connector, $event);

    expect($event->refresh()->external_id)->toBe('all_day_event_123');
});
