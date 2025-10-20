<?php

namespace App\Dtos\CurrenciesExchangers;

use Spatie\LaravelData\Data;

class CurrencyExchangerApiConfigDto extends Data
{
    public function __construct(
        public CurrencyExchangerApiConfigUriDto $uri,
        public int $cacheRequestsForSeconds = 60 * 60,
        public string $apiKey = '',
    )
    {
    }
}
