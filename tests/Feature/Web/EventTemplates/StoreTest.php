<?php

use App\Models\EventTemplate;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'event-templates');

it('creates event template successfully', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    post(route('event-templates.store'), [
        'calendar_id' => $calendar->id,
        'name' => 'Test Template',
        'event_title' => 'Test Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
        'event_description' => 'Test Description',
    ])
        ->assertRedirect(route('event-templates.index'))
        ->assertSessionHas('success');

    assertDatabaseHas('event_templates', [
        'calendar_id' => $calendar->id,
        'name' => 'Test Template',
        'event_title' => 'Test Event',
        'event_description' => 'Test Description',
    ]);
});

it('redirects to new when redirect_to is new', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    post(route('event-templates.store'), [
        'calendar_id' => $calendar->id,
        'name' => 'Test Template',
        'event_title' => 'Test Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
        'redirect_to' => 'new',
    ])
        ->assertRedirect(route('event-templates.create'));
});

it('redirects to show when redirect_to is show', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    $response = post(route('event-templates.store'), [
        'calendar_id' => $calendar->id,
        'name' => 'Test Template',
        'event_title' => 'Test Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
        'redirect_to' => 'show',
    ]);

    $eventTemplate = EventTemplate::where('name', 'Test Template')->sole();

    $response->assertRedirect(route('event-templates.show', $eventTemplate));
});

it('redirects unauthenticated users to login', function (): void {
    $calendar = calendarFactory()->create();

    post(route('event-templates.store'), [
        'calendar_id' => $calendar->id,
        'name' => 'Test Template',
        'event_title' => 'Test Event',
    ])
        ->assertRedirect(route('login'));
});

it('prevents user from creating event template in another users calendar', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();

    post(route('event-templates.store'), [
        'calendar_id' => $otherCalendar->id,
        'name' => 'Test Template',
        'event_title' => 'Test Event',
        'event_all_day' => false,
        'event_timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('calendar_id');
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        actingAsUser();

        post(route('event-templates.store'), [])
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

        post(route('event-templates.store'), [
            'calendar_id' => 99999,
            'name' => 'Test Template',
            'event_title' => 'Test Event',
        ])
            ->assertSessionHasErrors('calendar_id');
    });

    it('validates name must be string', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 12345,
            'event_title' => 'Test Event',
        ])
            ->assertSessionHasErrors('name');
    });

    it('validates name must not exceed 255 characters', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => str_repeat('a', 256),
            'event_title' => 'Test Event',
        ])
            ->assertSessionHasErrors('name');
    });

    it('validates event_title must be string', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 'Test Template',
            'event_title' => 12345,
        ])
            ->assertSessionHasErrors('event_title');
    });

    it('validates event_title must not exceed 255 characters', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 'Test Template',
            'event_title' => str_repeat('a', 256),
        ])
            ->assertSessionHasErrors('event_title');
    });

    it('validates event_timezone is required when event_all_day is false', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 'Test Template',
            'event_title' => 'Test Event',
            'event_all_day' => false,
            'event_description' => null,
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('event_timezone');
    });

    it('validates event_description must be string', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 'Test Template',
            'event_title' => 'Test Event',
            'event_all_day' => false,
            'event_timezone' => 'UTC',
            'event_description' => 12345,
        ])
            ->assertSessionHasErrors('event_description');
    });

    it('allows null event_description', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 'Test Template',
            'event_title' => 'Test Event',
            'event_all_day' => false,
            'event_timezone' => 'UTC',
            'event_description' => null,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    });

    it('strips disallowed HTML tags from event_description', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create();

        post(route('event-templates.store'), [
            'calendar_id' => $calendar->id,
            'name' => 'Test Template',
            'event_title' => 'Test Event',
            'event_all_day' => false,
            'event_timezone' => 'UTC',
            'event_description' => '<p>Hello</p><script>alert("xss")</script><strong>World</strong><div>Test</div>',
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $eventTemplate = EventTemplate::where('name', 'Test Template')->sole();

        expect($eventTemplate->event_description)->toBe('<p>Hello</p>alert("xss")<strong>World</strong>Test');
    });
});
