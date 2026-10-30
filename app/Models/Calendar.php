<?php

namespace App\Models;

use App\Builders\CalendarBuilder;
use App\Observers\CalendarObserver;
use Database\Factories\CalendarFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Override;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

#[ObservedBy([CalendarObserver::class])]
class Calendar extends Model
{
    /** @use HasFactory<CalendarFactory> */
    use HasFactory;

    /** @use HasBuilder<CalendarBuilder> */
    use HasBuilder;

    use SoftDeletes;
    use HasRelationships;

    #[Override]
    protected static string $builder = CalendarBuilder::class;

    #[Override]
    protected $with = ['connectorsForUser'];

    #[Override]
    protected $appends = ['connector', 'provider'];

    #[Override]
    protected $fillable = [
        'external_id',
        'name',
        'description',
        'enabled',
        'synchronized_at',
    ];

    /**
     * @return array{
     *     enabled: 'boolean',
     *     synchronized_at: 'datetime'
     * }
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'synchronized_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<Event, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->chaperone();
    }

    /**
     * @return BelongsToMany<Connector, $this>
     */
    public function connectors(): BelongsToMany
    {
        return $this->belongsToMany(Connector::class);
    }

    /**
     * @return BelongsToMany<Connector, $this, Pivot>
     */
    public function connectorsForUser(?User $user = null): BelongsToMany
    {
        $userId = $user?->id;
        $userId ??= Auth::id();

        return $this->belongsToMany(Connector::class)
            ->where('connectors.user_id', $userId);
    }

    /**
     * @return Attribute<Connector, never>
     */
    protected function connector(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('connectorsForUser')
                ? $this->connectorsForUser->sole()
                : $this->connectorsForUser()->sole()
        );
    }

    /**
     * @return HasManyDeep<User, $this>
     */
    public function users(): HasManyDeep
    {
        return $this->hasManyDeep(
            related: User::class,
            through: ['calendar_connector', Connector::class],
            foreignKeys: ['calendar_id', 'id', 'id'],
            localKeys: ['id', 'connector_id', 'user_id']
        )->distinct();
    }

    /**
     * @return Attribute<null|Provider, never>
     */
    protected function provider(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('connectorsForUser')) {
                    $connector = $this->connectorsForUser->first();
                }
                $connector ??= $this->connectors->firstOrFail();

                return $connector->provider;
            }
        );
    }
}
