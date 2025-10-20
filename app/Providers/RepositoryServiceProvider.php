<?php

namespace App\Providers;

use App\Repositories\Apis\GuzzleApiRepository;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use App\Repositories\CurrencyExchangers\SwopCxCurrencyExchanger;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @var class-string<CurrencyExchangerInterface>
     */
    public static string $currencyExchanger = SwopCxCurrencyExchanger::class;

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app
            ->bind(
                CurrencyExchangerInterface::class,
                function (): CurrencyExchangerInterface {
                    $config = static::$currencyExchanger::getConfig();

                    $api = new GuzzleApiRepository(
                        new Client(['base_uri' => $config->uri->base]),
                    );

                    return new static::$currencyExchanger(
                        $api,
                        $config,
                    );
                },
            );
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function provides(): array
    {
        return [
            CurrencyExchangerInterface::class,
        ];
    }
}
