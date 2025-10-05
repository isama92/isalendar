<?php

namespace App\Models;

use App\Builders\EventBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    /** @use \Illuminate\Database\Eloquent\HasBuilder<\App\Builders\EventBuilder> */
    use HasBuilder;

    use SoftDeletes;

    protected static string $builder = EventBuilder::class;

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
