<?php

namespace App\Dtos\CurrenciesExchangers;

use App\Dtos\Casters\ToUpperCaseCast;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Size;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CurrencyDto extends Data
{
    public function __construct(
        #[Size(3)]
        #[WithCast(ToUpperCaseCast::class)]
        public string $code,
        public int $numericCode,
        public int $decimalDigits,
        public string $name,
        public bool $active,
    )
    {
    }
}
