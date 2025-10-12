<?php

use App\Models\Connector;
use App\Models\Provider;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses()->group('web', 'providers');

describe('providers create', function (): void {
    it('displays create page with available providers for authenticated user', function (): void {
        $provider = Provider::googleCalendar();

        $user = User::factory()->create();
        Connector::factory()->for($user)->for($provider)->create();
        Connector::factory()->for(User::factory()->create())->for($provider)->create();

        actingAs($user);

        get(route('providers.create'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Providers/Create')
                ->has('providers', Provider::enabled()->count())
                ->where('providers.0.id', $provider->id)
                ->where('providers.0.name', $provider->name)
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
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();

        // Create connector for other user, it should not redirect here
        Connector::factory()->for($provider)->create();

        $connector = Connector::factory()->for($user)->for($provider)->create();

        actingAs($user);

        post(route('providers.store'), [
            'provider_id' => $provider->id,
        ])
            ->assertRedirect(route('calendars.create', $connector));
    });

    it('redirects to provider auth route when no connector exists', function (): void {
        $user = User::factory()->create();
        $provider = Provider::googleCalendar();

        actingAs($user);

        post(route('providers.store'), [
            'provider_id' => $provider->id,
        ])
            ->assertRedirect(route($provider->auth_route));
    });

    it('redirects unauthenticated users to login', function (): void {
        $provider = Provider::googleCalendar();

        post(route('providers.store'), [
            'provider_id' => $provider->id,
        ])
            ->assertRedirect(route('login'));
    });

    it('returns 404 when provider is disabled', function (): void {
        $user = User::factory()->create();
        $provider = Provider::microsoftOutlook();

        expect($provider->enabled)->toBeFalse();

        actingAs($user);

        post(route('providers.store'), [
            'provider_id' => $provider->id,
        ])
            ->assertNotFound();
    });

    it('returns validation error when provider does not exist', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        post(route('providers.store'), [
            'provider_id' => 99999,
        ])
            ->assertSessionHasErrors('provider_id');
    });

    it('requires provider_id field', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        post(route('providers.store'), [])
            ->assertSessionHasErrors('provider_id');
    });
});
