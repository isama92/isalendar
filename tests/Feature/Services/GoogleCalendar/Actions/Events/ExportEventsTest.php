<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Events\ExportEvents;
use App\Services\Calendar\GoogleCalendar\Actions\Events\PushEvent;

use function Pest\Laravel\partialMock;

uses()->group('gcalendar', 'gcalendar.events');

it('calls PushEvent for each event in the time window', function (): void {
    $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
    $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();

    $connector->calendars()->attach($calendar);

    // Create 3 events within the window
    $eventsInWindow = Event::factory()
        ->for($calendar)
        ->count(3)
        ->create([
            'starts_at' => now(),
            'ends_at' => now()->addHour(),
        ]);

    // Create 1 event outside the window (should not be exported)
    Event::factory()
        ->for($calendar)
        ->create([
            'starts_at' => now()->addMonths(3),
            'ends_at' => now()->addMonths(3)->addHour(),
        ]);

    $pushEventMock = partialMock(PushEvent::class);

    $eventsInWindow->each(function (Event $event) use ($pushEventMock, $connector): void {
        $pushEventMock->shouldReceive('execute')
            ->once()
            ->with($connector, Mockery::on(fn ($arg): bool => $arg->id === $event->id))
            ->andReturnUndefined();
    });

    app(ExportEvents::class)->execute($connector, $calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});

it('handles calendars with no events', function (): void {
    $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
    $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();
    $connector = Connector::factory()->create();
    $calendar = Calendar::factory()->create();

    $connector->calendars()->attach($calendar);

    $pushEventMock = partialMock(PushEvent::class);
    $pushEventMock->shouldNotReceive('execute');

    app(ExportEvents::class)->execute($connector, $calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});
