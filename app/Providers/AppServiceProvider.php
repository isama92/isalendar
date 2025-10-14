<?php

namespace App\Providers;

use App\Enums\Role;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(fn ($user, $ability): ?true => $user->hasRole(Role::SUPER_ADMIN->value) ? true : null);

        RateLimiter::for('connector-sync', fn (Request $request) => Limit::perHour(1)
            ->by(sprintf('%s:%s', $request->user()?->id, $request->route('connector'))));
    }
}
