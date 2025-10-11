<?php

namespace App\Builders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\App\Models\Calendar>
 */
class CalendarBuilder extends Builder
{
    public function forCurrentUser(bool $skipSuperAdmin = false): self
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return $this;
        }

        if (! $skipSuperAdmin && $user->hasRole(Role::SUPER_ADMIN)) {
            return $this;
        }

        return $this->whereRelation('connectors', 'user_id', $user->id);
    }

    public function enabled(): self
    {
        return $this->where('enabled', true);
    }
}
