<?php

namespace App\Dtos\CurrenciesExchangers\Collections;

use App\Dtos\CurrenciesExchangers\CurrencyRateDto;
use Illuminate\Support\Collection;

class CurrencyRateCollection extends Collection
{
    public function keyByQuotedCurrency(): self
    {
        return $this->keyBy(
            function (CurrencyRateDto $currencyRate): string {
                return $currencyRate->quoteCurrency->code;
            },
        );
    }
}
