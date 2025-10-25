<?php

namespace App\Models;

use App\Builders\ConnectorBuilder;
use App\Casts\ProviderAccessDetailsCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connector extends Model
{
    /** @use HasFactory<\Database\Factories\ConnectorFactory> */
    use HasFactory;

    /** @use \Illuminate\Database\Eloquent\HasBuilder<\App\Builders\ConnectorBuilder> */
    use HasBuilder;

    use SoftDeletes;

    protected static string $builder = ConnectorBuilder::class;

    protected $fillable = [
        'provider_id',
        'external_id',
        'access_details',
        'enabled',
    ];

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
