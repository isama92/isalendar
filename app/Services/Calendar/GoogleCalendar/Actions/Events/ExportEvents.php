<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Events;

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Spatie\QueueableAction\QueueableAction;

class ExportEvents
{
    use QueueableAction;

    public function __construct(
        private readonly PushEvent $pushEvent,
        private readonly DeleteEvent $deleteEvent,
    ) {}

    public function execute(
        Connector $connector,
        Calendar $calendar,
        CarbonImmutable $fetchWindowStart,
        CarbonImmutable $fetchWindowEnd,
    ): void {
        /** @phpstan-ignore method.notFound */
        $calendar->events()
            ->with(['lastModifiedBy', 'createdBy', 'calendar'])
            ->withMarkedForDeletion()
            ->whereBetween('starts_at', [$fetchWindowStart, $fetchWindowEnd])
            ->lazyById()
            ->each(fn (Event $event) => $this->pushEvent($connector, $event));
    }

    private function pushEvent(Connector $connector, Event $event): void
    {
        Cache::lock(sprintf('calendar-sync-%s-%s', $event->calendar->id, $event->id), 10)
            ->block(5, function () use ($connector, $event): void {
                if (! is_null($event->marked_for_deletion_at)) {
                    $this->deleteEvent->execute($connector, $event);

                    return;
                }

                $this->pushEvent->execute($connector, $event);
            });
    }
}
