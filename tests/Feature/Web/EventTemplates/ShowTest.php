<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventTemplateFactory;

uses()->group('web', 'event-templates');

it('displays event template for authenticated user', function (): void {
    actingAsUser();
    $eventTemplate = eventTemplateFactory()->create();

    get(route('event-templates.show', $eventTemplate))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Show')
            ->has('eventTemplate')
            ->where('eventTemplate.name', $eventTemplate->name)
            ->where('eventTemplate.event_title', $eventTemplate->event_title)
            ->where('eventTemplate.event_description', $eventTemplate->event_description)
            ->where('eventTemplate.event_all_day', $eventTemplate->event_all_day)
            ->where('eventTemplate.calendar.name', $eventTemplate->calendar->name)
        );
});

it('redirects unauthenticated users to login', function (): void {
    $eventTemplate = eventTemplateFactory()->create();

    get(route('event-templates.show', $eventTemplate))
        ->assertRedirect(route('login'));
});

it('prevents user from viewing another users event template', function (): void {
    actingAsUser();

    $otherUser = User::factory()->create();
    $otherConnector = connectorFactory()->for($otherUser)->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherTemplate = eventTemplateFactory()->for($otherUser)->for($otherCalendar)->create();

    get(route('event-templates.show', $otherTemplate))
        ->assertForbidden();
});
