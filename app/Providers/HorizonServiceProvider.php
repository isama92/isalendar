<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Override;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    #[Override]
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (User $user): bool => $user->hasRole(Role::SUPER_ADMIN));
    }
}
