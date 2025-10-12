<?php

use App\Actions\GetProviderCalendars;
use App\Data\CalendarData;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\post;

uses()->group('web', 'calendars');

describe('calendars create', function (): void {
    it('displays create page with available calendars for authenticated user', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

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

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->has('calendars', 2)
                ->has('provider')
                ->where('provider.id', $provider->id)
                ->where('provider.name', $provider->name)
                ->where('connectorRouteKey', $connector->getRouteKey())
            );
    });

    it('redirects unauthenticated users to login', function (): void {
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($provider)->create();

        get(route('calendars.create', $connector))
            ->assertRedirect(route('login'));
    });

    it('prevents unauthorized user from accessing create page', function (): void {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($otherUser)->for($provider)->create();

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertNotFound();
    });

    it('returns 404 when connector provider is disabled', function (): void {
        $user = User::factory()->create();
        $provider = Provider::microsoftOutlook();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertNotFound();
    });

    it('returns 404 when connector does not belong to authenticated user', function (): void {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($otherUser)->for($provider)->create();

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertNotFound();
    });

    it('filters out existing calendars from connector', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        // Create an existing calendar
        $existingCalendar = Calendar::factory()->create(['external_id' => 'existing-ext-id']);
        $existingCalendar->connectors()->attach($connector);

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

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->has('calendars', 1)
                ->where('calendars.0.external_id', 'new-ext-id')
            );
    });

    it('stores calendar list in session', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

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

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertSessionHas('calendars.create.list');
    });

    it('shows correct provider information', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

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

        actingAs($user);

        get(route('calendars.create', $connector))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Calendars/Create')
                ->where('provider.id', $provider->id)
                ->where('provider.name', $provider->name)
                ->has('provider.image_path')
            );
    });

    it('shows empty calendars list when all calendars already exist', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $existingCalendar = Calendar::factory()->create(['external_id' => 'ext-1']);
        $existingCalendar->connectors()->attach($connector);

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

        actingAs($user);

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
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: 'Calendar description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertRedirect(route('calendars.index'))
            ->assertSessionHas('success');
    });

    it('creates calendar in database', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: 'Calendar description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        $this->assertDatabaseHas('calendars', [
            'external_id' => 'ext-1',
            'name' => 'New Calendar',
            'description' => 'Calendar description',
        ]);
    });

    it('attaches calendar to connector', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: null
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect(Calendar::where('external_id', 'ext-1')->sole())
            ->not->toBeNull()
            ->connectors()->count()->toBe(1)
            ->connectors()->first()->id->toBe($connector->id);
    });

    it('enables connector after calendar creation', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create(['enabled' => false]);

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Calendar',
            description: null
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

        expect($connector->enabled)->toBeFalse();

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect($connector->fresh()->enabled)->toBeTrue();
    });

    it('redirects unauthenticated users to login', function (): void {
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($provider)->create();

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
            ->assertNotFound();
    });

    it('returns 404 when connector provider is disabled', function (): void {
        $user = User::factory()->create();
        $provider = Provider::microsoftOutlook();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertNotFound();
    });

    it('returns 404 when connector does not belong to authenticated user', function (): void {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($otherUser)->for($provider)->create();

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ])
            ->assertNotFound();
    });

    it('requires external_id field', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        actingAs($user);

        post(route('calendars.store', $connector), [])
            ->assertSessionHasErrors('external_id');
    });

    it('validates external_id must be string', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 12345,
        ])
            ->assertSessionHasErrors('external_id');
    });

    it('validates external_id must not exceed 255 characters', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => str_repeat('a', 256),
        ])
            ->assertSessionHasErrors('external_id');
    });

    it('validates external_id must not already exist for this connector', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $existingCalendar = Calendar::factory()->create(['external_id' => 'existing-ext-id']);
        $existingCalendar->connectors()->attach($connector);

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'existing-ext-id',
        ])
            ->assertSessionHasErrors('external_id');
    });

    it('restores soft-deleted calendar if it exists', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        // Create and soft delete a calendar
        $calendar = Calendar::factory()->create(['external_id' => 'ext-1', 'name' => 'Old Name']);
        $calendar->delete();

        expect(Calendar::find($calendar->id))->toBeNull()
            ->and(Calendar::withTrashed()->find($calendar->id))->not->toBeNull();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'New Name',
            description: 'Updated description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

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
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $calendar = Calendar::factory()->create([
            'external_id' => 'ext-1',
            'name' => 'Original Name',
            'description' => 'Original description',
        ]);
        $calendar->delete();

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'Updated Name',
            description: 'Updated description'
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect(Calendar::where('external_id', 'ext-1')->sole())
            ->name->toBe('Updated Name')
            ->description->toBe('Updated description');
    });

    it('does not attach calendar twice if already attached', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();
        $connector = Connector::factory()->for($user)->for($provider)->create();

        $calendar = Calendar::factory()->create(['external_id' => 'ext-1']);
        $calendar->connectors()->attach($connector);

        $calendarData = new CalendarData(
            externalId: 'ext-1',
            name: 'Calendar Name',
            description: null
        );

        session()->put('calendars.create.list', collect([$calendarData]));

        actingAs($user);

        post(route('calendars.store', $connector), [
            'external_id' => 'ext-1',
        ]);

        expect($calendar->connectors()->count())->toBe(1);
    });
});
