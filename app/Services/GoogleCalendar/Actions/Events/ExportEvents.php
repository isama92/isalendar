<?php

namespace App\Services\GoogleCalendar\Actions\Events;

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Spatie\QueueableAction\QueueableAction;

class ExportEvents
{
    use QueueableAction;

    public function __construct(
        private readonly PushEvent $pushEvent,
    ) {}

    public function execute(
        Connector $connector,
        Calendar $calendar,
        CarbonImmutable $fetchWindowStart,
        CarbonImmutable $fetchWindowEnd,
    ): void {
        $calendar->events()
            ->whereBetween('starts_at', [$fetchWindowStart, $fetchWindowEnd])
            ->lazyById()
            ->each(function (Event $event) use ($connector): void {
                $this->pushEvent->execute($connector, $event);
            });
    }
}
