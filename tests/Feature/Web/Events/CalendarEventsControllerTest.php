<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('renders the calendar page for authenticated users', function (): void {
    actingAsUser();

    get(route('events.calendar'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Events/Calendar'));
});

it('redirects unauthenticated users to login', function (): void {
    get(route('events.calendar'))
        ->assertRedirect(route('login'));
});

it('passes events to the calendar view', function (): void {
    actingAsUser();
    eventFactory(3)->create();

    get(route('events.calendar'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Calendar')
            ->has('events', 3)
        );
});

it('only shows events for the authenticated user', function (): void {
    $user = actingAsUser();
    eventFactory(2)->create();

    // Create events for another user
    $anotherUser = User::factory()->create();
    eventFactory(3)->for(calendarFactory(
        connector: connectorFactory()->for($anotherUser)->create()
    ))->create();

    get(route('events.calendar'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Calendar')
            ->has('events', 2)
        );
});

it('includes calendar relationship data', function (): void {
    actingAsUser();
    eventFactory()->create();

    get(route('events.calendar'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Events/Calendar')
            ->has('events.0.calendar')
            ->has('events.0.calendar.name')
        );
});
