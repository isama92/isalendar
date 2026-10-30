<?php

namespace App\Builders;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends Builder<Event>
 */
class EventBuilder extends Builder
{
    public function forCurrentUser(): self
    {
        return $this->whereRelation('calendar.connectors', 'user_id', Auth::id());
    }
}
