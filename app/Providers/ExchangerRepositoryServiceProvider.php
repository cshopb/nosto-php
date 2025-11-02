<?php

namespace App\Providers;

use App\Repositories\Apis\GuzzleApiRepository;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use App\Repositories\CurrencyExchangers\SwopCxCurrencyExchanger;
use App\Repositories\Monitoring\Interface\MonitoringRepositoryInterface;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ExchangerRepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
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
                function (Application $app): CurrencyExchangerInterface {
                    $config = static::$currencyExchanger::getConfig();
                    $monitoring = $app->make(MonitoringRepositoryInterface::class);

                    $api = new GuzzleApiRepository(
                        new GuzzleClient(['base_uri' => $config->uri->base]),
                        $monitoring,
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
