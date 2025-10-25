<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\App\Models\Calendar>
 */
class CalendarBuilder extends Builder
{
    public function forCurrentUser(): self
    {
        return $this->whereRelation('connectors', 'user_id', Auth::id());
    }

    public function enabled(): self
    {
        return $this->where('enabled', true);
    }
}
