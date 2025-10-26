<?php

namespace App\Dtos\Casters;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class ToUpperCaseCast implements Cast
{
    /**
     * @inheritDoc
     */
    public function cast(
        DataProperty $property,
        mixed $value,
        array $properties,
        CreationContext $context,
    ): mixed
    {
        if (is_string($value) === false) {
            return $value;
        }

        return strtoupper($value);
    }
}
