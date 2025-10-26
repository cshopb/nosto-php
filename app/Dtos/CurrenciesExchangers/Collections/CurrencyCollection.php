<?php

namespace App\Dtos\CurrenciesExchangers\Collections;

use App\Dtos\CurrenciesExchangers\CurrencyDto;
use Illuminate\Support\Collection;

class CurrencyCollection extends Collection
{
    public function keyByCode(): self
    {
        return $this->keyBy(
            function (CurrencyDto $currency): string {
                return strtoupper($currency->code);
            },
        );
    }
}
