<?php

namespace App\Dtos\CurrenciesExchangers;

use App\Dtos\CurrenciesExchangers\Casters\CurrencyUserInputSanitizeCaster;
use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\Size;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class CurrencyExchangerRequestDto extends Data
{
    public const string DEFAULT_BASE_CURRENCY = 'EUR';
    public const string DEFAULT_QUOTE_CURRENCY = 'USD';

    public function __construct(
        #[Size(3)]
        #[WithCast(CurrencyUserInputSanitizeCaster::class)]
        public string $baseCurrency = self::DEFAULT_BASE_CURRENCY,
        #[Size(3)]
        #[WithCast(CurrencyUserInputSanitizeCaster::class)]
        public string $quoteCurrency = self::DEFAULT_QUOTE_CURRENCY,
        public bool $isInertiaRequest = false,
    )
    {
    }

    public static function fromRequest(Request $request): self
    {
        return self::from(
            [
                'isInertiaRequest' => $request->header(
                    key: 'x-inertia',
                    default: false,
                ),
                ...$request->toArray(),
            ],
        );
    }
}
