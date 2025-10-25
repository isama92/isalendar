<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'event-templates');

it('redirects unauthenticated users to login', function (): void {
    get(route('event-templates.create'))
        ->assertRedirect(route('login'));
});

it('displays user calendars for selection', function (): void {
    actingAsUser();
    $calendar1 = calendarFactory()->create(['name' => 'Calendar 1']);
    $calendar2 = calendarFactory()->disabled()->create(['name' => 'Calendar 2']);

    get(route('event-templates.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Form')
            ->has('calendars', 2)
            ->where('calendars.0.id', $calendar1->id)
            ->where('calendars.1.id', $calendar2->id)
            ->has('timezones')
        );
});

it('does not display other users calendars', function (): void {
    actingAsUser();

    $userCalendar = calendarFactory()->create();

    $otherConnector = connectorFactory()->for(User::factory())->create();

    calendarFactory(connector: $otherConnector)->create();

    get(route('event-templates.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Form')
            ->has('calendars', 1)
            ->where('calendars.0.id', $userCalendar->id)
        );
});
