<?php

use App\Enums\Service;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'events');

it('redirects unauthenticated users to login', function (): void {
    get(route('events.index'))
        ->assertRedirect(route('login'));
});

it('shows correct events with properties, calendar names, and without pagination', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $calendar1 = Calendar::factory()->create(['name' => 'Work Calendar']);
    $calendar2 = Calendar::factory()->create(['name' => 'Personal Calendar']);

    $calendar1->connectors()->attach($connector);
    $calendar2->connectors()->attach($connector);

    Event::factory()->for($calendar1)->create([
        'title' => 'Work Event',
        'all_day' => true,
        'description' => 'Event description',
    ]);
    Event::factory()->count(25)->for($calendar2)->create();

    actingAs($user);

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('events', 26)
            ->where('events.0.all_day', true)
            ->where('events.0.description', 'Event description')
            ->where('events.0.calendar.name', 'Work Calendar')
            ->where('events.1.calendar.name', 'Personal Calendar')
        );
});

it('only shows authenticated user events', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();

    $userCalendar = Calendar::factory()->create();
    $userCalendar->connectors()->attach($connector);

    $userEvent = Event::factory()->for($userCalendar)->create();

    $otherUser = User::factory()->create();
    $otherConnector = Connector::factory()->for($otherUser)->create();
    $otherCalendar = Calendar::factory()->create();
    $otherCalendar->connectors()->attach($otherConnector);

    Event::factory()->for($otherCalendar)->create();

    actingAs($user);

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('events', 1)
            ->where('events.0.id', $userEvent->id)
        );
});

it('shows enabled providers', function (): void {
    $user = User::factory()->create();
    Provider::forceCreate([
        'name' => 'disabled',
        'image_path' => '',
        'access_object' => '',
        'auth_route' => '',
        'enabled' => false,
    ]);

    actingAs($user);

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('providers', 1)
            ->where('providers.0.id', Service::GOOGLE_CALENDAR->id())
        );
});

it('shows events from multiple connectors', function (): void {
    $user = User::factory()->create();

    $provider1 = Provider::googleCalendar();
    $provider2 = Provider::microsoftOutlook();

    $connector1 = Connector::factory()->for($user)->for($provider1, 'provider')->create();
    $connector2 = Connector::factory()->for($user)->for($provider2, 'provider')->create();

    $calendar1 = Calendar::factory()->create();
    $calendar2 = Calendar::factory()->create();

    $calendar1->connectors()->attach($connector1);
    $calendar2->connectors()->attach($connector2);

    Event::factory()->for($calendar1)->create();
    Event::factory()->for($calendar2)->create();

    actingAs($user);

    get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Index')
            ->has('events', 2)
        );
});
