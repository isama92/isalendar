<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventTemplateFactory;

uses()->group('web', 'event-templates');

it('redirects unauthenticated users to login', function (): void {
    get(route('event-templates.index'))
        ->assertRedirect(route('login'));
});

it('shows correct event templates with properties and calendar names', function (): void {
    actingAsUser();
    $calendar1 = calendarFactory()->create(['name' => 'Work Calendar']);
    $calendar2 = calendarFactory()->create(['name' => 'Personal Calendar']);

    eventTemplateFactory()->for($calendar1)->create([
        'name' => 'Meeting Template',
        'event_title' => 'Work Meeting',
        'event_all_day' => true,
        'event_description' => 'Template description',
    ]);
    eventTemplateFactory()->for($calendar2)->create();

    get(route('event-templates.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Index')
            ->has('eventTemplates', 2)
            ->where('eventTemplates.0.name', 'Meeting Template')
            ->where('eventTemplates.0.event_title', 'Work Meeting')
            ->where('eventTemplates.0.event_all_day', true)
            ->where('eventTemplates.0.event_description', 'Template description')
            ->where('eventTemplates.0.calendar.name', 'Work Calendar')
            ->where('eventTemplates.1.calendar.name', 'Personal Calendar')
        );
});

it('only shows authenticated user event templates', function (): void {
    actingAsUser();

    $userTemplate = eventTemplateFactory()->create();

    $otherUser = User::factory()->create();
    $otherConnector = connectorFactory()->for($otherUser)->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    eventTemplateFactory()->for($otherUser)->for($otherCalendar)->create();

    get(route('event-templates.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Index')
            ->has('eventTemplates', 1)
            ->where('eventTemplates.0.id', $userTemplate->id)
        );
});

it('shows enabled calendars', function (): void {
    actingAsUser();

    $enabledCalendar = calendarFactory()->create(['name' => 'Enabled Calendar']);
    $disabledCalendar = calendarFactory()->disabled()->create(['name' => 'Disabled Calendar']);

    get(route('event-templates.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('EventTemplates/Index')
            ->has('calendars', 1)
            ->where('calendars.0.id', $enabledCalendar->id)
        );
});
