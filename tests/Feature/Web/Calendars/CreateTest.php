<?php

use App\Actions\GetProviderCalendars;
use App\Data\CalendarData;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\post;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'calendars');

describe('calendars create', function (): void {
    it('displays create page with available calendars for authenticated user', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        $calendarDataCollection = collect([
            new CalendarData(
                externalId: 'ext-1',
                name: 'Calendar 1',
                description: 'Description 1'
            ),
            new CalendarData(
                externalId: 'ext-2',
                name: 'Calendar 2',
                description: null
            ),
        ]);

        mock(GetProviderCalendars::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn($calendarDataCollection);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->has('calendars', 2)
                ->has('provider')
                ->where('provider.id', $connector->provider->id)
                ->where('provider.name', $connector->provider->name)
                ->where('connectorRouteKey', $connector->getRouteKey())
            );
    });

    it('redirects unauthenticated users to login', function (): void {
        $connector = connectorFactory()->create();

        get(route('calendars.create', $connector))
            ->assertRedirect(route('login'));
    });

    it('prevents unauthorized user from accessing create page', function (): void {
        actingAsUser();
        $connector = connectorFactory()->for(User::factory())->create();

        get(route('calendars.create', $connector))
            ->assertForbidden();
    });

    it('returns 404 when connector provider is disabled', function (): void {
        actingAsUser();
        $connector = connectorFactory()->for(Provider::microsoftOutlook())->create();

        get(route('calendars.create', $connector))
            ->assertNotFound();
    });

    it('returns 403 when connector does not belong to authenticated user', function (): void {
        actingAsUser();
        $connector = connectorFactory()->for(User::factory())->create();

        get(route('calendars.create', $connector))
            ->assertForbidden();
    });

    it('filters out existing calendars from connector', function (): void {
        actingAsUser();
        $connector = calendarFactory()->create(['external_id' => 'existing-ext-id'])
            ->connectors->sole();

        $calendarDataCollection = collect([
            new CalendarData(
                externalId: 'existing-ext-id',
                name: 'Existing Calendar',
                description: null
            ),
            new CalendarData(
                externalId: 'new-ext-id',
                name: 'New Calendar',
                description: null
            ),
        ]);

        mock(GetProviderCalendars::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn($calendarDataCollection);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->has('calendars', 1)
                ->where('calendars.0.external_id', 'new-ext-id')
            );
    });

    it('stores calendar list in session', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        $calendarDataCollection = collect([
            new CalendarData(
                externalId: 'ext-1',
                name: 'Calendar 1',
                description: null
            ),
        ]);

        mock(GetProviderCalendars::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn($calendarDataCollection);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertSessionHas('calendars.create.list');
    });

    it('shows correct provider information', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        $calendarDataCollection = collect([
            new CalendarData(
                externalId: 'ext-1',
                name: 'Calendar 1',
                description: null
            ),
        ]);

        mock(GetProviderCalendars::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn($calendarDataCollection);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->where('provider.id', $connector->provider->id)
                ->where('provider.name', $connector->provider->name)
                ->has('provider.image_path')
            );
    });

    it('shows empty calendars list when all calendars already exist', function (): void {
        actingAsUser();
        $connector = calendarFactory()->create(['external_id' => 'ext-1'])
            ->connectors->sole();

        $calendarDataCollection = collect([
            new CalendarData(
                externalId: 'ext-1',
                name: 'Existing Calendar',
                description: null
            ),
        ]);

        mock(GetProviderCalendars::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn($calendarDataCollection);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->has('calendars', 0)
            );
    });
});

describe('calendars store', function (): void {
    it('creates calendar successfully', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: 'Calendar description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertRedirect(route('calendars.index'))
            ->assertSessionHas('success');
    });

    it('creates calendar in database', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: 'Calendar description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        assertDatabaseHas('calendars', [
            'external_id' => 'ext-1',
            'name' => 'New Calendar',
            'description' => 'Calendar description',
        ]);
    });

    it('attaches calendar to connector', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: null
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect(Calendar::where('external_id', 'ext-1')->sole())
            ->not->toBeNull()
            ->connectors()->count()->toBe(1)
            ->connectors()->first()->id->toBe($connector->id);
    });

    it('enables connector after calendar creation', function (): void {
        actingAsUser();
        $connector = connectorFactory()->disabled()->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: null
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        expect($connector->enabled)->toBeFalse();

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect($connector->fresh()->enabled)->toBeTrue();
    });

    it('redirects unauthenticated users to login', function (): void {
        $connector = connectorFactory()->create();

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertRedirect(route('login'));
    });

    it('prevents unauthorized user from storing calendar', function (): void {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($otherUser)->for($provider)->create();

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertForbidden();
    });

    it('returns 404 when connector provider is disabled', function (): void {
        actingAsUser();
        $connector = connectorFactory()->for(Provider::microsoftOutlook())->create();

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertNotFound();
    });

    it('returns 403 when connector does not belong to authenticated user', function (): void {
        actingAsUser();
        $otherConnector = connectorFactory()->for(User::factory())->create();

        post(route('calendars.store', $otherConnector), [
            'external_id' => 'ext-1',
        ])
            ->assertForbidden();
    });

    it('requires external_id field', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        post(route('calendars.store', $connector), [])
            ->assertSessionHasErrors('external_id');
    });

    it('validates external_id must be string', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        post(route('calendars.store', $connector), [
            'external_id' => 12345,
        ])
            ->assertSessionHasErrors('external_id');
    });

    it('validates external_id must not exceed 255 characters', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();

        post(route('calendars.store', $connector), [
            'external_id' => str_repeat('a', 256),
        ])
            ->assertSessionHasErrors('external_id');
    });

    it('validates external_id must not already exist for this connector', function (): void {
        actingAsUser();
        $connector = calendarFactory()->create(['external_id' => 'existing-ext-id'])
            ->connectors->sole();

        post(route('calendars.store', $connector), [
            'external_id' => 'existing-ext-id',
        ])
            ->assertSessionHasErrors('external_id');
    });

    it('restores soft-deleted calendar if it exists', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create(['external_id' => 'ext-1', 'name' => 'Old Name']);
        $connector = $calendar->connectors->sole();

        $calendar->delete();

        expect(Calendar::find($calendar->id))->toBeNull()
            ->and(Calendar::withTrashed()->find($calendar->id))->not->toBeNull();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Name',
            description: 'Updated description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect(Calendar::where('external_id', 'ext-1')->sole())
            ->not->toBeNull()
            ->id->toBe($calendar->id)
            ->name->toBe('New Name')
            ->description->toBe('Updated description')
            ->trashed()->toBeFalse();
    });

    it('updates existing calendar data when restoring', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create([
            'external_id' => 'ext-1',
            'name' => 'Original Name',
            'description' => 'Original description',
        ]);
        $connector = $calendar->connectors->sole();

        $calendar->delete();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'Updated Name',
            description: 'Updated description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect(Calendar::where('external_id', 'ext-1')->sole())
            ->name->toBe('Updated Name')
            ->description->toBe('Updated description');
    });

    it('does not attach calendar twice if already attached', function (): void {
        actingAsUser();
        $calendar = calendarFactory()->create(['external_id' => 'ext-1']);
        $connector = $calendar->connectors->sole();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'Calendar Name',
            description: null
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect($calendar->connectors()->count())->toBe(1);
    });
});
