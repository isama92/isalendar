<?php

namespace App\Models;

use App\Builders\EventTemplateBuilder;
use Database\Factories\EventTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class EventTemplate extends Model
{
    /** @use HasFactory<EventTemplateFactory> */
    use HasFactory;

    /** @use HasBuilder<EventTemplateBuilder> */
    use HasBuilder;

    use SoftDeletes;

    #[Override]
    protected static string $builder = EventTemplateBuilder::class;

    #[Override]
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }
}
