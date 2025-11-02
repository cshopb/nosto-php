<?php

namespace App\Dtos\CurrenciesExchangers;

use DateTimeImmutable;
use Spatie\LaravelData\Data;

class CurrencyExchangerApiConfigUriDto extends Data
{
    public function __construct(
        public string $base,
        public string $listOfRates,
        public string $availableCurrencies,
    )
    {
    }

    public function getListOfRatesUri(
        CurrencyDto $currency,
        DateTimeImmutable $date = new DateTimeImmutable(),
    ): string
    {
        $dateString = $date->format('Y-m-d');

        return $this->listOfRates . "?date=$dateString&base_currency=$currency->code";
    }
}
