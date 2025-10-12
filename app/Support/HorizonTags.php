<?php

namespace App\Support;

use App\Enums\Service;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Event;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class HorizonTags
{
    /**
     * @return string[]
     */
    public static function for(mixed ...$objects): array
    {
        $tags = [];

        foreach ($objects as $object) {
            switch (true) {
                case $object instanceof Calendar:
                    $tags[] = static::forEnum($object->connectors->first()->provider->service());
                    $tags[] = static::forModel($object);
                    break;

                case $object instanceof Connector:
                    $object->loadMissing(['provider', 'user']);
                    $tags[] = static::forEnum($object->provider->service());
                    $tags[] = static::forModel($object);
                    $tags[] = static::forModel($object->user);
                    break;

                case $object instanceof Event:
                    $object->loadMissing(['calendar']);
                    $tags[] = static::forEnum($object->calendar->connector->first()->provider->service());
                    $tags[] = static::forModel($object);
                    break;

                case $object instanceof Provider:
                    $tags[] = static::forEnum($object->service());
                    break;

                case $object instanceof Service:
                    $tags[] = static::forEnum($object);
                    break;
            }
        }

        return array_unique($tags);
    }

    public static function forModel(Model $model): string
    {
        return $model::class.':'.$model->getKey();
    }

    public static function forEnum(UnitEnum $enum): string
    {
        return $enum::class.':'.$enum->name;
    }
}
