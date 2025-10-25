<?php

namespace App\Models;

use App\Builders\EventTemplateBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\EventTemplateFactory> */
    use HasFactory;

    /** @use \Illuminate\Database\Eloquent\HasBuilder<\App\Builders\EventTemplateBuilder> */
    use HasBuilder;

    use SoftDeletes;

    protected static string $builder = EventTemplateBuilder::class;

    protected $fillable = [
        'calendar_id',
        'name',
        'event_title',
        'event_all_day',
        'event_timezone',
        'event_description',
    ];

    /**
     * @return array{
     *     event_all_day: 'boolean'
     * }
     */
    protected function casts(): array
    {
        return [
            'event_all_day' => 'boolean',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }
}
