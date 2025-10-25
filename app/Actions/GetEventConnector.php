<?php

namespace App\Actions;

use App\Models\Connector;
use App\Models\Event;

class GetEventConnector
{
    public function execute(Event $event): ?Connector
    {
        // Try to use the creator's connector
        if ($event->createdBy && $event->createdBy->enabled) {
            return $event->createdBy;
        }

        // Fallback: any enabled connector for this calendar
        return $event->calendar->connectors()
            ->enabled()
            ->first();
    }
}
