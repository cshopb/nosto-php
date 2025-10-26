<?php

namespace App\Dtos\CurrenciesExchangers;

use App\Dtos\Casters\ToUpperCaseCast;
use App\Dtos\CurrenciesExchangers\Normalizers\CurrencyExchangerRequestNormalizer;
use Spatie\LaravelData\Attributes\Validation\Size;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Normalizers\ArrayNormalizer;

class CurrencyExchangerRequestDto extends Data
{
    public function __construct(
        #[Size(3)]
        #[WithCast(ToUpperCaseCast::class)]
        public string $baseCurrency = 'EUR',
        #[Size(3)]
        #[WithCast(ToUpperCaseCast::class)]
        public string $quoteCurrency = 'USD',
    )
    {
    }
}
