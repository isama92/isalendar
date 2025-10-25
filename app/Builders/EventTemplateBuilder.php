<?php

namespace App\Builders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\App\Models\Event>
 */
class EventTemplateBuilder extends Builder
{
    public function forCurrentUser(): self
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return $this;
        }

        if ($user->hasRole(Role::SUPER_ADMIN)) {
            return $this;
        }

        return $this->whereBelongsTo($user);
    }
}
