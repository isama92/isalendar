<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

uses()->group('web', 'events');

it('updates event successfully', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $event = Event::factory()->for($calendar)->create([
        'title' => 'Old Title',
        'description' => 'Old Description',
    ]);

    actingAs($user);

    put(route('events.update', $event), [
        'calendar_id' => $calendar->id,
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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $event = Event::factory()->for($calendar)->create();

    actingAs($user);

    put(route('events.update', $event), [
        'calendar_id' => $calendar->id,
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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    $event = Event::factory()->for($calendar)->create();

    actingAs($user);

    put(route('events.update', $event), [
        'calendar_id' => $calendar->id,
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
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();

    $calendar1->connectors()->attach($connector);
    $calendar2->connectors()->attach($connector);

    $event = Event::factory()->for($calendar1)->create();

    actingAs($user);

    expect($event->calendar_id)->toBe($calendar1->id);

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
    $calendar = Calendar::factory()->create();
    $event = Event::factory()->for($calendar)->create();

    put(route('events.update', $event), [
        'calendar_id' => $calendar->id,
        'title' => 'Updated Event',
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
    ])
        ->assertRedirect(route('login'));
});

it('prevents user from updating another users event', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    $otherEvent = Event::factory()->for($otherCalendar)->create([
        'title' => 'Original Title',
    ]);

    actingAs($user);

    put(route('events.update', $otherEvent), [
        'calendar_id' => $otherCalendar->id,
        'title' => 'Hacked Title',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
    ])
        ->assertForbidden();

    expect($otherEvent->fresh())
        ->title->toBe('Original Title');
});

it('prevents user from moving event to another users calendar', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userConnector = Connector::factory()->for($user)->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $userCalendar = Calendar::factory()->create();
    $otherCalendar = Calendar::factory()->create();

    $userCalendar->connectors()->attach($userConnector);
    $otherCalendar->connectors()->attach($otherConnector);

    $event = Event::factory()->for($userCalendar)->create();

    actingAs($user);

    put(route('events.update', $event), [
        'calendar_id' => $otherCalendar->id,
        'title' => 'Updated Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('calendar_id');

    expect($event->fresh()->calendar_id)->toBe($userCalendar->id);
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

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
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => 99999,
            'title' => 'Updated Event',
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('calendar_id');
    });

    it('validates title must be string', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
            'title' => 12345,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('title');
    });

    it('validates title must not exceed 255 characters', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
            'title' => str_repeat('a', 256),
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('title');
    });

    it('validates ends_at must be after or equal to starts_at', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 11:00:00',
            'ends_at' => '2025-01-15 10:00:00',
            'timezone' => 'UTC',
        ])
            ->assertSessionHasErrors('ends_at');
    });

    it('validates timezone is required when all_day is false', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
            'title' => 'Updated Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
        ])
            ->assertSessionHasErrors('timezone');
    });

    it('validates description must be string', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
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
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create([
            'description' => 'Original Description',
        ]);

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
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
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        $event = Event::factory()->for($calendar)->create();

        actingAs($user);

        put(route('events.update', $event), [
            'calendar_id' => $calendar->id,
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
