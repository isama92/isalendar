<?php

namespace App\Models;

use App\Casts\ProviderAccessDetailsCast;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connector extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'provider_id',
        'external_id',
        'access_details',
        'enabled',
    ];

    /**
     * @return array{
     *     enabled: 'boolean'
     * }
     */
    protected function casts(): array
    {
        return [
            'access_details' => ProviderAccessDetailsCast::class,
            'enabled' => 'boolean',
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Connector>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Connector>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Provider, $this>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Calendar, $this>
     */
    public function calendars(): BelongsToMany
    {
        return $this->belongsToMany(Calendar::class);
    }
}
