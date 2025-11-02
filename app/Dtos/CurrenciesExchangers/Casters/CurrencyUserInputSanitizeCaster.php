<?php

namespace App\Dtos\CurrenciesExchangers\Casters;

use Illuminate\Support\Str;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class CurrencyUserInputSanitizeCaster implements Cast
{
    public function cast(
        DataProperty $property,
        mixed $value,
        array $properties,
        CreationContext $context,
    ): string|Uncastable
    {
        if (is_string($value) === false) {
            return Uncastable::create();
        }

        return Str::of($value)->stripTags()
            ->trim()
            ->upper()
            ->toString();
    }
}
