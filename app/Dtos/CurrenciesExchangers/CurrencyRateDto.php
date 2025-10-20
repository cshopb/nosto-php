<?php

namespace App\Dtos\CurrenciesExchangers;

use App\Dtos\CurrenciesExchangers\Casters\CurrencyCaster;
use DateTimeImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CurrencyRateDto extends Data
{
    public function __construct(
        #[WithCast(CurrencyCaster::class)]
        public CurrencyDto $baseCurrency,
        #[WithCast(CurrencyCaster::class)]
        public CurrencyDto $quoteCurrency,
        public float $quote,
        public DateTimeImmutable $date,
    )
    {
    }
}
