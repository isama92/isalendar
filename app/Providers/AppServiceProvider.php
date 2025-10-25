<?php

namespace App\Providers;

use App\Enums\Role;
use App\Services\Auth\Oidc\OidcProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(fn ($user, $ability): ?true => $user->hasRole(Role::SUPER_ADMIN->value) ? true : null);

        $this->bootRateLimiters();

        $this->bootOidcSocialiteProvider();
    }

    private function bootRateLimiters(): void
    {
        RateLimiter::for('ui-calendar-sync', fn (Request $request) => Limit::perHour(1)
            ->by(sprintf('%s:%s', $request->user()?->id, $request->route('calendar'))));
    }

    private function bootOidcSocialiteProvider(): void
    {
        $socialite = $this->app->make(SocialiteFactory::class);

        $socialite->extend('oidc', function (array $app) use ($socialite) {
            $config = $app['config']['services.oidc'];

            return $socialite->buildProvider(OidcProvider::class, $config);
        });
    }
}
