<?php

namespace App\Providers;

use App\Repositories\Apis\GuzzleApiRepository;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use App\Repositories\CurrencyExchangers\SwopCxCurrencyExchanger;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use InfluxDB2\Client as InfluxDbClient;

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
                    $influxDb = App::make(InfluxDbClient::class);

                    $api = new GuzzleApiRepository(
                        new GuzzleClient(['base_uri' => $config->uri->base]),
                        $influxDb,
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
