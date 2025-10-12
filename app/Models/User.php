<?php

namespace App\Models;

use App\Observers\UserObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use HasRelationships;

    protected $with = ['settings'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array{
     *     email_verified_at: 'datetime',
     *     password: 'hashed'
     * }
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\UserSetting, $this>
     */
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Connector, $this>
     */
    public function connectors(): HasMany
    {
        return $this->hasMany(Connector::class);
    }

    /**
     * if paginating $user->calendars()->distinct('calendars.id')->paginate()
     * use the distinct to get unique records
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep<\App\Models\Calendar, $this>
     */
    public function calendars(): HasManyDeep
    {
        return $this->hasManyDeep(
            related: Calendar::class,
            through: [Connector::class, 'calendar_connector'],
            foreignKeys: ['user_id', 'connector_id', 'id'],
            localKeys: ['id', 'id', 'calendar_id']
        )->distinct();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, null>
     */
    protected function photoPath(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf('%s.webp', $this->id),
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, null>
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf('%s %s', $this->first_name, $this->last_name),
        );
    }
}
