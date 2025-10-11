<?php

namespace App\Models;

use App\Builders\CalendarBuilder;
use App\Observers\CalendarObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MultipleItemsFoundException;

#[ObservedBy([CalendarObserver::class])]
class Calendar extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarFactory> */
    use HasFactory;

    /** @use \Illuminate\Database\Eloquent\HasBuilder<\App\Builders\CalendarBuilder> */
    use HasBuilder;

    use SoftDeletes;

    protected static string $builder = CalendarBuilder::class;

    protected $appends = ['connector'];

    protected $fillable = [
        'external_id',
        'name',
        'description',
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
            'enabled' => 'boolean',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Connector, $this>
     */
    public function connectors(): BelongsToMany
    {
        // TODO: it's actually 1-n (1 conn - n cals) when considering a user, can that be simplified?
        return $this->belongsToMany(Connector::class);
    }

    /**
     * @return Attribute<\App\Models\Connector, never>
     */
    protected function connector(): Attribute
    {
        return Attribute::make(
            get: function () {
                $userId = Auth::id();

                $connectors = $this->relationLoaded('connectors')
                    ? $this->connectors->where('user_id', $userId)
                    : $this->connectors()->where('user_id', $userId)->get();

                if ($connectors->count() > 1) {
                    throw new MultipleItemsFoundException($connectors->count());
                }

                return $connectors->first();
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Event, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
