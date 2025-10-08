<?php

namespace App\Casts;

use App\Contracts\AccessToken;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Override;

/**
 * @implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes<null|\App\Contracts\AccessToken, null|\App\Contracts\AccessToken>
 *
 * @SuppressWarnings("PHPMD.ErrorControlOperator")
 */
class ProviderAccessDetailsCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    #[Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): ?AccessToken
    {
        if (is_null($value)) {
            return null;
        }

        $string = Crypt::decryptString((string) $value);

        return @unserialize($string, ['allowed_classes' => true]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    #[Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $accessToken = ensure($value, AccessToken::class);

        $string = serialize($accessToken);

        return Crypt::encryptString($string);
    }
}
