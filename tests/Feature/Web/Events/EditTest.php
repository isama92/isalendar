<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'events');

it('displays edit page with event data for authenticated user', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create(['name' => 'Calendar 1']);
    $calendar2 = Calendar::factory()->disabled()->create(['name' => 'Calendar 2']);

    $calendar1->connectors()->attach($connector);
    $calendar2->connectors()->attach($connector);

    $event = Event::factory()->for($calendar1)->create([
        'title' => 'Test Event',
        'description' => 'Test Description',
    ]);

    actingAs($user);

    get(route('events.edit', $event))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Form')
            ->has('timezones')
            ->has('event')
            ->where('event.id', $event->id)
            ->where('event.title', 'Test Event')
            ->where('event.description', 'Test Description')
            ->where('event.calendar_id', $calendar1->id)
            ->has('calendars', 2)
            ->where('calendars.0.id', $calendar1->id)
            ->where('calendars.1.id', $calendar2->id)
        );
});

it('redirects unauthenticated users to login', function (): void {
    $calendar = Calendar::factory()->create();
    $event = Event::factory()->for($calendar)->create();

    get(route('events.edit', $event))
        ->assertRedirect(route('login'));
});

it('prevents user from editing another users event', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    $otherEvent = Event::factory()->for($otherCalendar)->create();

    actingAs($user);

    get(route('events.edit', $otherEvent))
        ->assertForbidden();
});
