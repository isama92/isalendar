<?php

use App\Enums\Service;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'calendars');

describe('calendars index', function (): void {
    it('displays calendars for authenticated user', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars')
                ->has('providers')
            );
    });

    it('redirects unauthenticated users to login', function (): void {
        get(route('calendars.index'))
            ->assertRedirect(route('login'));
    });

    it('shows correct calendars with event counts', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar1 = Calendar::factory()->create();
        $calendar2 = Calendar::factory()->create();

        $calendar1->connectors()->attach($connector);
        $calendar2->connectors()->attach($connector);

        Event::factory()->count(5)->for($calendar1)->create();
        Event::factory()->count(3)->for($calendar2)->create();

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars', 2)
                ->where('calendars.0.events_count', 5)
                ->where('calendars.1.events_count', 3)
            );
    });

    it('only shows authenticated user calendars', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $userCalendar = Calendar::factory()->create();
        $userCalendar->connectors()->attach($connector);

        $otherUser = User::factory()->create();
        $otherConnector = Connector::factory()->for($otherUser)->create();
        $otherCalendar = Calendar::factory()->create();
        $otherCalendar->connectors()->attach($otherConnector);

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars', 1)
                ->where('calendars.0.id', $userCalendar->id)
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

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('providers', 1)
                ->where('providers.0.id', Service::GOOGLE_CALENDAR->id())
            );
    });

    it('displays empty state when no calendars', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars', 0)
            );
    });

    it('returns all calendars for frontend pagination', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        // Create 15 calendars - all should be returned for frontend pagination
        $calendars = Calendar::factory()->count(15)->create();

        foreach ($calendars as $calendar) {
            $calendar->connectors()->attach($connector);
        }

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars', 15)
            );
    });

    it('shows zero event count for calendars without events', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create(['name' => 'Empty Calendar']);
        $calendar->connectors()->attach($connector);

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars', 1)
                ->where('calendars.0.events_count', 0)
            );
    });

    it('shows multiple calendars for user with multiple connectors', function (): void {
        $user = User::factory()->create();

        $provider1 = Provider::googleCalendar();
        $provider2 = Provider::microsoftOutlook();

        $connector1 = Connector::factory()->for($user)->for($provider1, 'provider')->create();
        $connector2 = Connector::factory()->for($user)->for($provider2, 'provider')->create();

        $calendar1 = Calendar::factory()->create();
        $calendar2 = Calendar::factory()->create();

        $calendar1->connectors()->attach($connector1);
        $calendar2->connectors()->attach($connector2);

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars', 2)
            );
    });
});
