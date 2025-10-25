<?php

use App\Models\Event;
use App\Services\Calendar\GoogleCalendar\Actions\Events\DeleteEvent;
use App\Services\Calendar\GoogleCalendar\Actions\Events\ExportEvents;
use App\Services\Calendar\GoogleCalendar\Actions\Events\PushEvent;

use function Pest\Laravel\partialMock;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\eventFactory;

uses()->group('gcalendar', 'gcalendar.events');

it('calls PushEvent for each event in the time window', function (): void {
    $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
    $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

    // Create 3 events within the window
    $eventsInWindow = eventFactory(3)
        ->create([
            'starts_at' => now(),
            'ends_at' => now()->addHour(),
        ]);

    $calendar = $eventsInWindow->first()->calendar;

    // Create 1 event outside the window (should not be exported)
    eventFactory()
        ->for($calendar)
        ->create([
            'starts_at' => now()->addMonths(3),
            'ends_at' => now()->addMonths(3)->addHour(),
        ]);

    $pushEventMock = partialMock(PushEvent::class);

    $eventsInWindow->each(function (Event $event) use ($pushEventMock): void {
        $pushEventMock->shouldReceive('execute')
            ->once()
            ->with(Mockery::on(fn ($arg): bool => $arg->id === $event->id))
            ->andReturnUndefined();
    });

    app(ExportEvents::class)->execute($calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});

it('handles calendars with no events', function (): void {
    $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
    $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

    $calendar = calendarFactory()->create();

    $pushEventMock = partialMock(PushEvent::class);
    $pushEventMock->shouldNotReceive('execute');

    app(ExportEvents::class)->execute($calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});

it('calls DeleteEvent for events marked for deletion and not PushEvent', function (): void {
    $fetchWindowStart = now()->subWeek()->startOfDay()->toImmutable();
    $fetchWindowEnd = now()->addWeeks(2)->endOfDay()->toImmutable();

    $markedEvent = eventFactory()->create([
        'external_id' => 'event_to_delete',
        'title' => 'Marked for Deletion',
        'starts_at' => now(),
        'ends_at' => now()->addHour(),
        'all_day' => false,
        'timezone' => 'Europe/Amsterdam',
        'marked_for_deletion_at' => now(),
    ]);

    $deleteEventMock = partialMock(DeleteEvent::class);
    $deleteEventMock->shouldReceive('execute')
        ->once()
        ->with(Mockery::on(fn ($arg): bool => $arg->id === $markedEvent->id))
        ->andReturnUndefined();

    $pushEventMock = partialMock(PushEvent::class);
    $pushEventMock->shouldNotReceive('execute');

    app(ExportEvents::class)->execute($markedEvent->calendar, $fetchWindowStart, $fetchWindowEnd);

    expect(true)->toBeTrue();
});
