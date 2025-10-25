<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventTemplateFactory;

uses()->group('web', 'event-templates');

it('displays edit page with event template data for authenticated user', function (): void {
    actingAsUser();

    $calendar1 = calendarFactory()->create(['name' => 'Calendar 1']);
    $calendar2 = calendarFactory()->disabled()->create(['name' => 'Calendar 2']);

    $eventTemplate = eventTemplateFactory()->for($calendar1)->create([
        'name' => 'Test Template',
        'event_title' => 'Test Event',
        'event_description' => 'Test Description',
    ]);

    get(route('event-templates.edit', $eventTemplate))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Form')
            ->has('timezones')
            ->has('eventTemplate')
            ->where('eventTemplate.id', $eventTemplate->id)
            ->where('eventTemplate.name', 'Test Template')
            ->where('eventTemplate.event_title', 'Test Event')
            ->where('eventTemplate.event_description', 'Test Description')
            ->where('eventTemplate.calendar_id', $calendar1->id)
            ->has('calendars', 2)
            ->where('calendars.0.id', $calendar1->id)
            ->where('calendars.1.id', $calendar2->id)
        );
});

it('redirects unauthenticated users to login', function (): void {
    $eventTemplate = eventTemplateFactory()->create();

    get(route('event-templates.edit', $eventTemplate))
        ->assertRedirect(route('login'));
});

it('prevents user from editing another users event template', function (): void {
    actingAsUser();

    $otherUser = User::factory()->create();
    $otherConnector = connectorFactory()->for($otherUser)->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherTemplate = eventTemplateFactory()->for($otherUser)->for($otherCalendar)->create();

    get(route('event-templates.edit', $otherTemplate))
        ->assertForbidden();
});
