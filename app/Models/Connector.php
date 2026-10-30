<?php

namespace App\Models;

use App\Builders\ConnectorBuilder;
use App\Casts\ProviderAccessDetailsCast;
use Database\Factories\ConnectorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class Connector extends Model
{
    /** @use HasFactory<ConnectorFactory> */
    use HasFactory;

    /** @use HasBuilder<ConnectorBuilder> */
    use HasBuilder;

    use SoftDeletes;

    #[Override]
    protected static string $builder = ConnectorBuilder::class;

    #[Override]
    protected $fillable = [
        'provider_id',
        'external_id',
        'access_details',
        'enabled',
    ];

    #[Override]
    protected $hidden = [
        'access_details',
    ];

    /**
     * @return array{
     *     access_details: 'App\\Casts\\ProviderAccessDetailsCast',
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
     * @return BelongsTo<Provider, $this>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Calendar, $this>
     */
    public function calendars(): BelongsToMany
    {
        return $this->belongsToMany(Calendar::class);
    }
}
