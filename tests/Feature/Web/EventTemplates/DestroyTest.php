<?php

use App\Models\User;

use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventTemplateFactory;

uses()->group('web', 'event-templates');

it('allows user to delete their event template', function (): void {
    actingAsUser();
    $eventTemplate = eventTemplateFactory()->create();

    delete(route('event-templates.destroy', $eventTemplate))
        ->assertRedirect(route('event-templates.index'))
        ->assertSessionHas('success');

    assertSoftDeleted('event_templates', ['id' => $eventTemplate->id]);
});

it('prevents unauthenticated user from deleting event template', function (): void {
    $eventTemplate = eventTemplateFactory()->create();

    delete(route('event-templates.destroy', $eventTemplate))
        ->assertRedirect(route('login'));

    assertNotSoftDeleted('event_templates', ['id' => $eventTemplate->id]);
});

it('prevents user from deleting another users event template', function (): void {
    actingAsUser();

    $otherUser = User::factory()->create();
    $otherConnector = connectorFactory()->for($otherUser)->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherTemplate = eventTemplateFactory()->for($otherUser)->for($otherCalendar)->create();

    delete(route('event-templates.destroy', $otherTemplate))
        ->assertForbidden();

    assertNotSoftDeleted('event_templates', ['id' => $otherTemplate->id]);
});
