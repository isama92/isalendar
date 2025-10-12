<?php

use App\Enums\Service;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

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
                ->has('calendars.data', 2)
                ->where('calendars.data.0.events_count', 5)
                ->where('calendars.data.1.events_count', 3)
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
                ->has('calendars.data', 1)
                ->where('calendars.data.0.id', $userCalendar->id)
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
                ->has('calendars.data', 0)
            );
    });

    it('paginates results', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        // Create 15 calendars (more than the 10 per page limit)
        $calendars = Calendar::factory()->count(15)->create();

        foreach ($calendars as $calendar) {
            $calendar->connectors()->attach($connector);
        }

        actingAs($user);

        get(route('calendars.index'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Index')
                ->has('calendars.data', 10)
                ->has('calendars.links')
                ->where('calendars.total', 15)
                ->where('calendars.per_page', 10)
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
                ->has('calendars.data', 1)
                ->where('calendars.data.0.events_count', 0)
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
                ->has('calendars.data', 2)
            );
    });
});

describe('calendar toggle enable/disable', function (): void {
    it('can disable an enabled calendar', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create(['enabled' => true]);
        $calendar->connectors()->attach($connector);

        actingAs($user);

        put(route('calendars.update', $calendar), [
            'enabled' => false,
        ])
            ->assertRedirect(route('calendars.index'))
            ->assertSessionHas('success');

        expect($calendar->fresh()->enabled)->toBeFalse();
    });

    it('can enable a disabled calendar', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create(['enabled' => false]);
        $calendar->connectors()->attach($connector);

        actingAs($user);

        put(route('calendars.update', $calendar), [
            'enabled' => true,
        ])
            ->assertRedirect(route('calendars.index'))
            ->assertSessionHas('success');

        expect($calendar->fresh()->enabled)->toBeTrue();
    });

    it('prevents unauthenticated user from updating calendar', function (): void {
        $calendar = Calendar::factory()->create(['enabled' => true]);

        put(route('calendars.update', $calendar), [
            'enabled' => false,
        ])
            ->assertRedirect(route('login'));

        expect($calendar->fresh()->enabled)->toBeTrue();
    });

    it('prevents user from updating another users calendar', function (): void {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherConnector = Connector::factory()->for($otherUser)->create();

        $otherCalendar = Calendar::factory()->create(['enabled' => true]);
        $otherCalendar->connectors()->attach($otherConnector);

        actingAs($user);

        put(route('calendars.update', $otherCalendar), [
            'enabled' => false,
        ])
            ->assertForbidden();

        expect($otherCalendar->fresh()->enabled)->toBeTrue();
    });

    it('requires enabled field', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create(['enabled' => true]);
        $calendar->connectors()->attach($connector);

        actingAs($user);

        put(route('calendars.update', $calendar), [])
            ->assertSessionHasErrors('enabled');

        expect($calendar->fresh()->enabled)->toBeTrue();
    });

    it('validates enabled field must be boolean', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create(['enabled' => true]);
        $calendar->connectors()->attach($connector);

        actingAs($user);

        put(route('calendars.update', $calendar), [
            'enabled' => 'invalid',
        ])
            ->assertSessionHasErrors('enabled');

        expect($calendar->fresh()->enabled)->toBeTrue();
    });
});

describe('calendar deletion', function (): void {
    it('allows user to delete their calendar', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        actingAs($user);

        delete(route('calendars.destroy', $calendar))
            ->assertRedirect(route('calendars.index'))
            ->assertSessionHas('success');

        expect($calendar->connectors()->count())->toBe(0);
    });

    it('soft deletes calendar when no connectors remain', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        actingAs($user);

        delete(route('calendars.destroy', $calendar));

        expect(Calendar::find($calendar->id))->toBeNull()
            ->and(Calendar::withTrashed()->find($calendar->id))->not->toBeNull()
            ->and(Calendar::withTrashed()->find($calendar->id)->trashed())->toBeTrue();
    });

    it('does not delete calendar when other connectors remain', function (): void {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $connector1 = Connector::factory()->for($user1)->create();
        $connector2 = Connector::factory()->for($user2)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach([$connector1->id, $connector2->id]);

        actingAs($user1);

        delete(route('calendars.destroy', $calendar));

        expect($calendar->fresh())->not->toBeNull()
            ->and($calendar->connectors()->count())->toBe(1)
            ->and($calendar->connectors()->first()->id)->toBe($connector2->id);
    });

    it('deletes connector when no calendars remain', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        actingAs($user);

        delete(route('calendars.destroy', $calendar));

        expect(Connector::find($connector->id))->toBeNull();
    });

    it('does not delete connector when other calendars remain', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar1 = Calendar::factory()->create();
        $calendar2 = Calendar::factory()->create();

        $calendar1->connectors()->attach($connector);
        $calendar2->connectors()->attach($connector);

        actingAs($user);

        delete(route('calendars.destroy', $calendar1));

        expect(Connector::find($connector->id))->not->toBeNull()
            ->and($connector->calendars()->count())->toBe(1)
            ->and($connector->calendars()->first()->id)->toBe($calendar2->id);
    });

    it('prevents unauthenticated user from deleting calendar', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        delete(route('calendars.destroy', $calendar))
            ->assertRedirect(route('login'));

        expect($calendar->connectors()->count())->toBe(1);
    });

    it('prevents user from deleting another users calendar', function (): void {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherConnector = Connector::factory()->for($otherUser)->create();

        $otherCalendar = Calendar::factory()->create();
        $otherCalendar->connectors()->attach($otherConnector);

        actingAs($user);

        delete(route('calendars.destroy', $otherCalendar))
            ->assertForbidden();

        expect($otherCalendar->connectors()->count())->toBe(1);
    });

    it('detaches calendar from connector on deletion', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        actingAs($user);

        expect($calendar->connectors()->count())->toBe(1);

        delete(route('calendars.destroy', $calendar));

        expect($calendar->connectors()->count())->toBe(0);
    });

    it('soft deletes all events when calendar is deleted', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $calendar = Calendar::factory()->create();
        $calendar->connectors()->attach($connector);

        Event::factory()->count(5)->for($calendar)->create();

        actingAs($user);

        expect($calendar->events()->count())->toBe(5);

        delete(route('calendars.destroy', $calendar));

        expect(Event::where('calendar_id', $calendar->id)->count())->toBe(0);
    });
});
