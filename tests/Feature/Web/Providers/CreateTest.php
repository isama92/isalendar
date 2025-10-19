<?php

use App\Enums\Service;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'providers');

describe('providers create', function (): void {
    it('displays create page with available providers for authenticated user', function (): void {
        actingAsUser();
        $connector = connectorFactory()->create();
        connectorFactory()->for(User::factory())->create();

        get(route('providers.create'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Providers/Create')
                ->has('providers', Provider::enabled()->count())
                ->where('providers.0.id', $connector->provider->id)
                ->where('providers.0.name', $connector->provider->name)
                ->has('providers.0.image_path')
                ->has('providers.0.connectors_count')
                ->missing('providers.0.access_object')
                ->missing('providers.0.auth_route')
                ->etc()
                ->where('providers.0.connectors_count', 1)
            );
    });

    it('redirects unauthenticated users to login', function (): void {
        get(route('providers.create'))
            ->assertRedirect(route('login'));
    });
});

describe('providers store', function (): void {
    it('redirects to calendars create when connector already exists', function (): void {
        actingAsUser();

        // Create connector for other user, it should not redirect here
        connectorFactory()->for(User::factory())->create();

        $connector = connectorFactory()->create();

        post(route('providers.store'), [
            'provider_id' => $connector->provider->id,
        ])
            ->assertRedirect(route('calendars.create', $connector));
    });

    it('redirects to provider auth route when no connector exists', function (): void {
        actingAsUser();
        $provider = Provider::googleCalendar();

        post(route('providers.store'), [
            'provider_id' => $provider->id,
        ])
            ->assertRedirect(route($provider->auth_route));
    });

    it('redirects unauthenticated users to login', function (): void {
        post(route('providers.store'), [
            'provider_id' => Service::GOOGLE_CALENDAR->id(),
        ])
            ->assertRedirect(route('login'));
    });

    it('returns 404 when provider is disabled', function (): void {
        actingAsUser();
        $provider = Provider::microsoftOutlook();

        expect($provider->enabled)->toBeFalse();

        post(route('providers.store'), [
            'provider_id' => $provider->id,
        ])
            ->assertNotFound();
    });

    it('returns validation error when provider does not exist', function (): void {
        actingAsUser();

        post(route('providers.store'), [
            'provider_id' => 99999,
        ])
            ->assertSessionHasErrors('provider_id');
    });

    it('requires provider_id field', function (): void {
        actingAsUser();

        post(route('providers.store'), [])
            ->assertSessionHasErrors('provider_id');
    });
});
