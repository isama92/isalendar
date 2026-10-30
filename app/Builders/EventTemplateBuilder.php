<?php

namespace App\Builders;

use App\Models\EventTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends Builder<EventTemplate>
 */
class EventTemplateBuilder extends Builder
{
    public function forCurrentUser(): self
    {
        return $this->whereBelongsTo(Auth::user());
    }
}
