<?php

namespace Tests\Helpers;

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\Provider;
use App\Models\User;
use Database\Factories\CalendarFactory;
use Database\Factories\ConnectorFactory;
use Database\Factories\EventFactory;

function connectorFactory(?int $times = null): ConnectorFactory
{
    return Connector::factory()
        ->when(! is_null($times))->count($times)
        ->for(Provider::googleCalendar())
        ->when(test()->user instanceof User)->for(test()->user);
}

function calendarFactory(?int $times = null, ?Connector $connector = null): CalendarFactory
{
    return Calendar::factory()
        ->when(! is_null($times))->count($times)
        ->hasAttached($connector ?? connectorFactory()->create());
}

function eventFactory(?int $times = null): EventFactory
{
    return Event::factory()
        ->when(! is_null($times))->count($times)
        ->for(calendarFactory());
}
