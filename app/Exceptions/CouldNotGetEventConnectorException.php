<?php

namespace App\Exceptions;

use App\Models\Event;
use Exception;

class CouldNotGetEventConnectorException extends Exception
{
    public function __construct(Event $event)
    {
        parent::__construct(sprintf('Could not get connector for event %s', $event->id));
    }
}
