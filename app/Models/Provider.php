<?php

namespace App\Models;

use App\Enums\Service;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Provider extends Model
{
    #[Override]
    public $timestamps = false;

    #[Override]
    protected $hidden = [
        'access_object',
        'auth_route',
    ];

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

    public static function microsoftOutlook(): self
    {
        return once(fn (): self => self::findOrFail(Service::MICROSOFT_OUTLOOK->id()));
    }

    /**
     * @param  Builder<Provider>  $query
     * @return Builder<Provider>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * @return HasMany<Connector, $this>
     */
    public function connectors(): HasMany
    {
        return $this->hasMany(Connector::class);
    }
}
