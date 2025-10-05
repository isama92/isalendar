<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'all_day',
        'starts_at',
        'ends_at',
        'description',
    ];

    /**
     * @return array{
     *     all_day: 'boolean',
     *     starts_at: 'datetime',
     *     ends_at: 'datetime'
     * }
     */
    protected function casts(): array
    {
        return [
            'all_day' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
