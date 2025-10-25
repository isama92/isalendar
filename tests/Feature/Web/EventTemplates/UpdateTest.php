<?php

use App\Models\User;

use function Pest\Laravel\put;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventTemplateFactory;

uses()->group('web', 'event-templates');

it('updates event template successfully', function (): void {
    actingAsUser();

    $eventTemplate = eventTemplateFactory()->create([
        'name' => 'Old Name',
        'event_title' => 'Old Title',
        'event_description' => 'Old Description',
    ]);

    put(route('event-templates.update', $eventTemplate), [
        'calendar_id' => $eventTemplate->calendar->id,
        'name' => 'New Name',
        'event_title' => 'New Title',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
        'event_description' => 'New Description',
    ])
        ->assertRedirect(route('event-templates.show', $eventTemplate))
        ->assertSessionHas('success');

    expect($eventTemplate->fresh())
        ->name->toBe('New Name')
        ->event_title->toBe('New Title')
        ->event_description->toBe('New Description');
});

it('redirects to edit when redirect_to is edit', function (): void {
    actingAsUser();
    $eventTemplate = eventTemplateFactory()->create();

    put(route('event-templates.update', $eventTemplate), [
        'calendar_id' => $eventTemplate->calendar->id,
        'name' => 'Updated Template',
        'event_title' => 'Updated Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
        'redirect_to' => 'edit',
    ])
        ->assertRedirect(route('event-templates.edit', $eventTemplate));
});

it('redirects to index when redirect_to is index', function (): void {
    actingAsUser();
    $eventTemplate = eventTemplateFactory()->create();

    put(route('event-templates.update', $eventTemplate), [
        'calendar_id' => $eventTemplate->calendar->id,
        'name' => 'Updated Template',
        'event_title' => 'Updated Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
        'redirect_to' => 'index',
    ])
        ->assertRedirect(route('event-templates.index'));
});

it('can move event template to different calendar', function (): void {
    actingAsUser();
    $eventTemplate = eventTemplateFactory()->create();
    $calendar1 = $eventTemplate->calendar;
    $calendar2 = calendarFactory()->create();

    put(route('event-templates.update', $eventTemplate), [
        'calendar_id' => $calendar2->id,
        'name' => 'Updated Template',
        'event_title' => 'Updated Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($eventTemplate->fresh()->calendar_id)->toBe($calendar2->id);
});

it('redirects unauthenticated users to login', function (): void {
    $eventTemplate = eventTemplateFactory()->create();

    put(route('event-templates.update', $eventTemplate), [
        'calendar_id' => $eventTemplate->calendar->id,
        'name' => 'Updated Template',
        'event_title' => 'Updated Event',
    ])
        ->assertRedirect(route('login'));
});

it('prevents user from updating another users event template', function (): void {
    actingAsUser();

    $otherUser = User::factory()->create();
    $otherConnector = connectorFactory()->for($otherUser)->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherTemplate = eventTemplateFactory()->for($otherUser)->for($otherCalendar)->create(['name' => 'Original Name']);

    put(route('event-templates.update', $otherTemplate), [
        'calendar_id' => $otherCalendar->id,
        'name' => 'Hacked Name',
        'event_title' => 'Hacked Title',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
    ])
        ->assertForbidden();

    expect($otherTemplate->fresh())->name->toBe('Original Name');
});

it('prevents user from moving event template to another users calendar', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $eventTemplate = eventTemplateFactory()->create();

    put(route('event-templates.update', $eventTemplate), [
        'calendar_id' => $otherCalendar->id,
        'name' => 'Updated Template',
        'event_title' => 'Updated Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('calendar_id');

    expect($eventTemplate->fresh()->calendar_id)->toBe($eventTemplate->calendar->id);
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [])
            ->assertSessionHasErrors([
                'calendar_id',
                'name',
                'event_title',
            ])
            ->assertSessionDoesntHaveErrors([
                'event_all_day',
                'event_timezone',
            ]);
    });

    it('validates calendar_id must exist', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => 99999,
            'name' => 'Updated Template',
            'event_title' => 'Updated Event',
        ])
            ->assertSessionHasErrors('calendar_id');
    });

    it('validates name must be string', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 12345,
            'event_title' => 'Updated Event',
        ])
            ->assertSessionHasErrors('name');
    });

    it('validates name must not exceed 255 characters', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => str_repeat('a', 256),
            'event_title' => 'Updated Event',
        ])
            ->assertSessionHasErrors('name');
    });

    it('validates event_title must be string', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 'Updated Template',
            'event_title' => 12345,
        ])
            ->assertSessionHasErrors('event_title');
    });

    it('validates event_title must not exceed 255 characters', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 'Updated Template',
            'event_title' => str_repeat('a', 256),
        ])
            ->assertSessionHasErrors('event_title');
    });

    it('validates event_timezone is required when event_all_day is false', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 'Updated Template',
            'event_title' => 'Updated Event',
            'event_all_day' => false,
        ])
            ->assertSessionHasErrors('event_timezone');
    });

    it('validates event_description must be string', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 'Updated Template',
            'event_title' => 'Updated Event',
            'event_all_day' => false,
            'event_timezone' => 'UTC',
            'event_description' => 12345,
        ])
            ->assertSessionHasErrors('event_description');
    });

    it('allows null event_description', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create(['event_description' => 'Original Description']);

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 'Updated Template',
            'event_title' => 'Updated Event',
            'event_all_day' => false,
            'event_timezone' => 'UTC',
            'event_description' => null,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        expect($eventTemplate->fresh()->event_description)->toBeNull();
    });

    it('strips disallowed HTML tags from event_description', function (): void {
        actingAsUser();
        $eventTemplate = eventTemplateFactory()->create();

        put(route('event-templates.update', $eventTemplate), [
            'calendar_id' => $eventTemplate->calendar->id,
            'name' => 'Updated Template',
            'event_title' => 'Updated Event',
            'event_all_day' => false,
            'event_timezone' => 'UTC',
            'event_description' => '<p>Hello</p><script>alert("xss")</script><strong>World</strong><div>Test</div>',
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        expect($eventTemplate->fresh()->event_description)->toBe('<p>Hello</p>alert("xss")<strong>World</strong>Test');
    });
});
