<?php

namespace App\Repositories\CurrencyExchangers\Interfaces;

use App\Dtos\CurrenciesExchangers\Collections\CurrencyCollection;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Dtos\CurrenciesExchangers\CurrencyExchangerApiConfigDto;
use App\Dtos\CurrenciesExchangers\CurrencyRateDto;
use App\Exceptions\CurrencyExchangerApiException;
use DateTimeImmutable;

interface CurrencyExchangerInterface
{
    public static function getConfig(): CurrencyExchangerApiConfigDto;

    public static function getProviderName(): string;

    public function getAvailableCurrencyCacheName(): string;

    public function getCurrencyRateCacheName(string $currencyCode): string;

    /**
     * @return CurrencyCollection<CurrencyDto>
     * @throws CurrencyExchangerApiException
     */
    public function getAvailableCurrencies(): CurrencyCollection;

    /**
     * @param string $currencyCode
     *
     * @return CurrencyDto|null
     * @throws CurrencyExchangerApiException
     */
    public function getCurrencyFromCode(string $currencyCode): ?CurrencyDto;

    /**
     * @param CurrencyDto $baseCurrency
     * @param CurrencyDto $quoteCurrency
     * @param DateTimeImmutable $date
     *
     * @return CurrencyRateDto|null
     * @throws CurrencyExchangerApiException
     */
    public function getRateForCurrencies(
        CurrencyDto $baseCurrency,
        CurrencyDto $quoteCurrency,
        DateTimeImmutable $date = new DateTimeImmutable(),
    ): ?CurrencyRateDto;
}
