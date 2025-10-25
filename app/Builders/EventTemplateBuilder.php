<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\App\Models\Event>
 */
class EventTemplateBuilder extends Builder
{
    public function forCurrentUser(): self
    {
        return $this->whereBelongsTo(Auth::user());
    }
}
