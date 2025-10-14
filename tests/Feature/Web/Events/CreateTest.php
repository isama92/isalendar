<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'events');

it('redirects unauthenticated users to login', function (): void {
    get(route('events.create'))
        ->assertRedirect(route('login'));
});

it('returns 403 when user has no connectors', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    get(route('events.create'))
        ->assertForbidden();
});

it('displays user calendars for selection', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create(['name' => 'Calendar 1']);
    $calendar2 = Calendar::factory()->disabled()->create(['name' => 'Calendar 2']);

    $calendar1->connectors()->attach($connector);
    $calendar2->connectors()->attach($connector);

    actingAs($user);

    get(route('events.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Form')
            ->has('calendars', 2)
            ->where('calendars.0.id', $calendar1->id)
            ->where('calendars.1.id', $calendar2->id)
            ->has('timezones')
        );
});

it('does not display other users calendars', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userConnector = Connector::factory()->for($user)->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();

    $userCalendar = Calendar::factory()->create();
    $otherCalendar = Calendar::factory()->create();

    $userCalendar->connectors()->attach($userConnector);
    $otherCalendar->connectors()->attach($otherConnector);

    actingAs($user);

    get(route('events.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Form')
            ->has('calendars', 1)
            ->where('calendars.0.id', $userCalendar->id)
        );
});
