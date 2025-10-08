<?php

namespace App\Models;

use App\Enums\Service;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    /**
     * @return array{
     *     enabled: 'boolean'
     * }
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function service(): Service
    {
        return Service::fromId($this->id);
    }

    public static function googleCalendar(): self
    {
        return once(fn (): self => self::findOrFail(Service::GOOGLE_CALENDAR->id()));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Provider>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Provider>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Connector, $this>
     */
    public function connectors(): HasMany
    {
        return $this->hasMany(Connector::class);
    }
}
