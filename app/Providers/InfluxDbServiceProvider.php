<?php

namespace App\Providers;

use App\Dtos\Config\InfluxDbConfigDto;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;

class InfluxDbServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(
            Client::class,
            function (Application $app) {
                $config = InfluxDbConfigDto::getFromConfig();

                return new Client(
                    [
                        'url' => $config->url,
                        'token' => $config->token,
                        'bucket' => $config->bucket,
                        'org' => $config->organization,
                        'precision' => WritePrecision::NS,
                    ],
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
            Client::class,
        ];
    }
}
