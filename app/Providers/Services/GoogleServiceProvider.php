<?php

namespace App\Providers\Services;

use Google\Client as GoogleClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Override;

class GoogleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const array ACCESS_SCOPES = [
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/calendar.events',
        'openid',
        'profile',
        'email',
    ];

    public const array ACCESS_PARAMS = [
        'access_type' => 'offline',
        'prompt' => 'consent',
    ];

    #[Override]
    public function register(): void
    {
        $this->app->scoped(GoogleClient::class, fn (Application $app): GoogleClient => new GoogleClient([
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => route('services.google.auth.redirect'),
        ]));
    }

    /**
     * @return class-string[]
     */
    #[Override]
    public function provides(): array
    {
        return [GoogleClient::class];
    }
}
