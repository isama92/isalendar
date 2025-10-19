<?php

use App\Models\User;

use function Pest\Laravel\put;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('updates event successfully', function (): void {
    actingAsUser();

    $event = eventFactory()->create([
        'title' => 'Old Title',
        'description' => 'Old Description',
    ]);

    put(route('events.update', $event), [
        'calendar_id' => $event->calendar->id,
        'title' => 'New Title',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
        'description' => 'New Description',
    ])
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHas('success');

    expect($event->fresh())
        ->title->toBe('New Title')
        ->description->toBe('New Description');
});

it('redirects to edit when redirect_to is edit', function (): void {
    actingAsUser();
    $event = eventFactory()->create();

    put(route('events.update', $event), [
        'calendar_id' => $event->calendar->id,
        'title' => 'Updated Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
        'redirect_to' => 'edit',
    ])
        ->assertRedirect(route('events.edit', $event));
});

it('redirects to index when redirect_to is index', function (): void {
    actingAsUser();
    $event = eventFactory()->create();

    put(route('events.update', $event), [
        'calendar_id' => $event->calendar->id,
        'title' => 'Updated Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
        'redirect_to' => 'index',
    ])
        ->assertRedirect(route('events.index'));
});

it('can move event to different calendar', function (): void {
    actingAsUser();
    $event = eventFactory()->create();
    $calendar1 = $event->calendar;
    $calendar2 = calendarFactory()->create();

    put(route('events.update', $event), [
        'calendar_id' => $calendar2->id,
        'title' => 'Updated Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($event->fresh()->calendar_id)->toBe($calendar2->id);
});

it('redirects unauthenticated users to login', function (): void {
    $event = eventFactory()->create();

    put(route('events.update', $event), [
        'calendar_id' => $event->calendar->id,
        'title' => 'Updated Event',
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
    ])
        ->assertRedirect(route('login'));
});

it('prevents user from updating another users event', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $otherEvent = eventFactory()->for($otherCalendar)->create(['title' => 'Original Title']);

    put(route('events.update', $otherEvent), [
        'calendar_id' => $otherCalendar->id,
        'title' => 'Hacked Title',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
    ])
        ->assertForbidden();

    expect($otherEvent->fresh())->title->toBe('Original Title');
});

it('prevents user from moving event to another users calendar', function (): void {
    actingAsUser();

    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();
    $event = eventFactory()->create();

    put(route('events.update', $event), [
        'calendar_id' => $otherCalendar->id,
        'title' => 'Updated Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('calendar_id');

    expect($event->fresh()->calendar_id)->toBe($event->calendar->id);
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [])
            ->assertSessionHasErrors([
                'calendar_id',
                'title',
                'starts_at',
                'ends_at',
            ])
            ->assertSessionDoesntHaveErrors([
                'all_day',
                'timezone',
            ]);
    });

    it('validates calendar_id must exist', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => 99999,
            'title' => 'Updated Event',
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('calendar_id');
    });

    it('validates title must be string', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => 12345,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('title');
    });

    it('validates title must not exceed 255 characters', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => str_repeat('a', 256),
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('title');
    });

    it('validates ends_at must be after or equal to starts_at', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 11:00:00',
            'ends_at' => '2025-01-15 10:00:00',
            'timezone' => 'UTC',
        ])
            ->assertSessionHasErrors('ends_at');
    });

    it('validates timezone is required when all_day is false', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('timezone');
    });

    it('validates description must be string', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
            'timezone' => 'UTC',
            'description' => 12345,
        ])
            ->assertSessionHasErrors('description');
    });

    it('allows null description', function (): void {
        actingAsUser();
        $event = eventFactory()->create(['description' => 'Original Description']);

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
            'timezone' => 'UTC',
            'description' => null,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        expect($event->fresh()->description)->toBeNull();
    });

    it('strips disallowed HTML tags from description', function (): void {
        actingAsUser();
        $event = eventFactory()->create();

        put(route('events.update', $event), [
            'calendar_id' => $event->calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
            'timezone' => 'UTC',
            'description' => '<p>Hello</p><script>alert("xss")</script><strong>World</strong><div>Test</div>',
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        expect($event->fresh()->description)->toBe('<p>Hello</p>alert("xss")<strong>World</strong>Test');
    });
});
