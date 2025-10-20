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

        // I don't know... When I pass the correct value,
        // I am getting 403 error and the message that I need to pay,
        // even though the documentation (https://swop.cx/pricing) says this would be free.
        // I tried to grab the data for "single rate" but I am getting the same error.
        // Just to progress, I am hardcoding the default free currency...
//        $currencyString = 'EUR';

        return $this->listOfRates . "?date=$dateString&base_currency=$currency->code";
    }
}
