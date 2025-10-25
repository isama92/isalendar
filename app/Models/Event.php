<?php

namespace App\Models;

use App\Builders\EventBuilder;
use App\Models\Scopes\ExcludeMarkedForDeletionScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy(ExcludeMarkedForDeletionScope::class)]
class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    /** @use \Illuminate\Database\Eloquent\HasBuilder<\App\Builders\EventBuilder> */
    use HasBuilder;

    use SoftDeletes;

    protected static string $builder = EventBuilder::class;

    protected $fillable = [
        'calendar_id',
        'created_by_connector_id',
        'last_modified_by_connector_id',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Connector, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Connector::class, 'created_by_connector_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Connector, $this>
     */
    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(Connector::class, 'last_modified_by_connector_id');
    }
}
