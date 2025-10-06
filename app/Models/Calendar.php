<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calendar extends Model
{
    protected $fillable = [
        'user_id',
        'external_user_id',
        'external_calendar_id',
        'external_token',
        'external_refresh_token',
        'external_token_expires_at',
    ];

    /**
     * @return array{
     *     external_token_expires_at: 'datetime'
     * }
     */
    protected function casts(): array
    {
        return [
            'external_token_expires_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Event, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
