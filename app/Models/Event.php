<?php

namespace App\Models;

use App\Builders\EventBuilder;
use App\Models\Scopes\ExcludeMarkedForDeletionScope;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

#[ScopedBy(ExcludeMarkedForDeletionScope::class)]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /** @use HasBuilder<EventBuilder> */
    use HasBuilder;

    use SoftDeletes;

    #[Override]
    protected static string $builder = EventBuilder::class;

    #[Override]
    protected $fillable = [
        'calendar_id',
        'created_by_connector_id',
        'external_id',
        'title',
        'all_day',
        'timezone',
        'starts_at',
        'ends_at',
        'description',
        'marked_for_deletion_at',
    ];

    /**
     * @return array{
     *     all_day: 'boolean',
     *     starts_at: 'datetime',
     *     ends_at: 'datetime',
     *     marked_for_deletion_at: 'datetime'
     * }
     */
    protected function casts(): array
    {
        return [
            'all_day' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'marked_for_deletion_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return BelongsTo<Connector, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Connector::class, 'created_by_connector_id');
    }
}
