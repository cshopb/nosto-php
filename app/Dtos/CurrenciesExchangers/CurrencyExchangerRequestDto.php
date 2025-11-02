<?php

namespace App\Dtos\CurrenciesExchangers;

use App\Dtos\CurrenciesExchangers\Casters\CurrencyUserInputSanitizeCaster;
use Spatie\LaravelData\Attributes\Validation\Size;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class CurrencyExchangerRequestDto extends Data
{
    public function __construct(
        #[Size(3)]
        #[WithCast(CurrencyUserInputSanitizeCaster::class)]
        public string $baseCurrency = 'EUR',
        #[Size(3)]
        #[WithCast(CurrencyUserInputSanitizeCaster::class)]
        public string $quoteCurrency = 'USD',
    )
    {
    }
}
