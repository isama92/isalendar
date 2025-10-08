<?php

namespace App\Providers;

use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(fn ($user, $ability): ?true => $user->hasRole(Role::SUPER_ADMIN->value) ? true : null);
    }
}
