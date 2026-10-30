<?php

namespace App\Builders;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends Builder<Calendar>
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
