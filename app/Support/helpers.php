<?php

use Illuminate\Support\Arr;

/**
 * @SuppressWarnings("PHPMD.MissingImport")
 */
if (! function_exists('ensure')) {
    /**
     * @template TEnsureOfType
     *
     * @param  array<array-key, class-string<TEnsureOfType>>|class-string<TEnsureOfType>  $type
     * @return TEnsureOfType
     *
     * @throws \UnexpectedValueException
     */
    function ensure(mixed $value, array|string $type): mixed
    {
        $allowedTypes = Arr::wrap($type);

        $valueType = get_debug_type($value);

        foreach ($allowedTypes as $allowedType) {
            if ($valueType === $allowedType || $value instanceof $allowedType) {
                return $value;
            }
        }

        throw new UnexpectedValueException(
            sprintf("Expected instance of [%s], but got '%s'.", implode(', ', $allowedTypes), $valueType)
        );
    }
}
