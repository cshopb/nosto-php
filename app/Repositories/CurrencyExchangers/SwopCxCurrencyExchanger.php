<?php

namespace App\Repositories\CurrencyExchangers;

use App\Dtos\Apis\ApiRequestOptionsDto;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyCollection;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyRateCollection;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Dtos\CurrenciesExchangers\CurrencyExchangerApiConfigDto;
use App\Dtos\CurrenciesExchangers\CurrencyRateDto;
use App\Exceptions\ApiCallException;
use App\Exceptions\CurrencyExchangerException;
use App\Repositories\Apis\Interfaces\ApiRepositoryInterface;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;

readonly class SwopCxCurrencyExchanger implements CurrencyExchangerInterface
{
    public function __construct(
        private ApiRepositoryInterface $api,
        private CurrencyExchangerApiConfigDto $config,
    )
    {
    }

    public static function getProviderName(): string
    {
        return 'swop-cx';
    }

    public static function getConfig(): CurrencyExchangerApiConfigDto
    {
        return CurrencyExchangerApiConfigDto::from(
            config(
                'api.' . static::getProviderName(),
            ),
        );
    }

    public function getAvailableCurrencyCacheName(): string
    {
        return static::getProviderName() . 'AvailableCurrencies';
    }

    public function getCurrencyRateCacheName(string $currencyCode): string
    {
        return static::getProviderName() . "SingleRate-$currencyCode";
    }

    /**
     * @inheritDoc
     */
    public function getAvailableCurrencies(): CurrencyCollection
    {
        try {
            return Cache::remember(
                $this->getAvailableCurrencyCacheName(),
                $this->config->cacheRequestsForSeconds,
                function (): CurrencyCollection {
                    $result = $this->api
                        ->get(
                            $this->config->uri->availableCurrencies,
                            ApiRequestOptionsDto::fromApiConfig($this->config),
                        );

                    // Pulled out so that the IDE recognises it correctly
                    $content = $result->content;

                    /** @var CurrencyCollection $result */
                    $result = CurrencyDto::collect(
                        $content,
                        CurrencyCollection::class,
                    );

                    return $result->keyByCode();
                },
            );
        } catch (ApiCallException $exception) {
            throw new CurrencyExchangerException(
                message: 'Error grabbing available currencies from ' . static::getProviderName(),
                previous: $exception,
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getCurrencyFromCode(string $currencyCode): ?CurrencyDto
    {
        /** @var CurrencyDto|null */
        return $this->getAvailableCurrencies()->get($currencyCode);
    }

    /**-
     * @inheritDoc
     */
    public function getRateForCurrency(
        CurrencyDto $baseCurrency,
        CurrencyDto $quoteCurrency,
        DateTimeImmutable $date = new DateTimeImmutable(),
    ): ?CurrencyRateDto
    {
        try {
            $result = Cache::remember(
                $this->getCurrencyRateCacheName($baseCurrency->code),
                $this->config->cacheRequestsForSeconds,
                function () use ($baseCurrency, $date): CurrencyRateCollection {
                    $result = $this->api->get(
                        $this->config->uri->getListOfRatesUri(
                            $baseCurrency,
                            $date,
                        ),
                        ApiRequestOptionsDto::fromApiConfig($this->config),
                    );

                    /** @var CurrencyRateCollection $collection */
                    $collection = CurrencyRateDto::collect(
                        $result->content,
                        CurrencyRateCollection::class,
                    );

                    return $collection->keyByQuotedCurrency();
                },
            );
        } catch (ApiCallException $exception) {
            throw new CurrencyExchangerException(
                message: "Error grabbing single rate for $baseCurrency->code from " . static::getProviderName(),
                previous: $exception,
            );
        }

        return $result->get($quoteCurrency->code);
    }
}
