<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

uses()->group('web', 'events');

it('creates event successfully', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    post(route('events.store'), [
        'calendar_id' => $calendar->id,
        'title' => 'Test Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
        'description' => 'Test Description',
    ])
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('success');

    assertDatabaseHas('events', [
        'calendar_id' => $calendar->id,
        'title' => 'Test Event',
        'description' => 'Test description',
    ]);
});

it('redirects to new when redirect_to is new', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    post(route('events.store'), [
        'calendar_id' => $calendar->id,
        'title' => 'Test Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
        'redirect_to' => 'new',
    ])
        ->assertRedirect(route('events.create'));
});

it('redirects to show when redirect_to is show', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    $response = post(route('events.store'), [
        'calendar_id' => $calendar->id,
        'title' => 'Test Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
        'redirect_to' => 'show',
    ]);

    $event = Event::where('title', 'Test Event')->sole();

    $response->assertRedirect(route('events.show', $event));
});

it('redirects unauthenticated users to login', function (): void {
    $calendar = Calendar::factory()->create();

    post(route('events.store'), [
        'calendar_id' => $calendar->id,
        'title' => 'Test Event',
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
    ])
        ->assertRedirect(route('login'));
});

it('prevents user from creating event in another users calendar', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    actingAs($user);

    post(route('events.store'), [
        'calendar_id' => $otherCalendar->id,
        'title' => 'Test Event',
        'all_day' => false,
        'starts_at' => '2025-01-15 10:00:00',
        'ends_at' => '2025-01-15 11:00:00',
        'timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('calendar_id');
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        post(route('events.store'), [])
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

        actingAs($user);

        post(route('events.store'), [
            'calendar_id' => 99999,
            'title' => 'Test Event',
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

        actingAs($user);

        post(route('events.store'), [
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

        actingAs($user);

        post(route('events.store'), [
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

        actingAs($user);

        post(route('events.store'), [
            'calendar_id' => $calendar->id,
            'title' => 'Test Event',
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

        actingAs($user);

        post(route('events.store'), [
            'calendar_id' => $calendar->id,
            'title' => 'Test Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
            'description' => null,
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('timezone');
    });

    it('validates description must be string', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        actingAs($user);

        post(route('events.store'), [
            'calendar_id' => $calendar->id,
            'title' => 'Test Event',
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

        actingAs($user);

        post(route('events.store'), [
            'calendar_id' => $calendar->id,
            'title' => 'Test Event',
            'all_day' => false,
            'starts_at' => '2025-01-15 10:00:00',
            'ends_at' => '2025-01-15 11:00:00',
            'timezone' => 'UTC',
            'description' => null,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    });
});
