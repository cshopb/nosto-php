<?php

namespace App\Dtos\CurrenciesExchangers;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CurrencyDto extends Data
{
    public function __construct(
        public string $code,
        public int $numericCode,
        public int $decimalDigits,
        public string $name,
        public bool $active,
    )
    {
    }
}
