<?php

namespace App\Providers;

use App\Dtos\Config\InfluxDbConfigDto;
use App\Repositories\Monitoring\InfluxDbMonitoringRepository;
use App\Repositories\Monitoring\Interface\MonitoringRepositoryInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;

class MonitoringServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(
            MonitoringRepositoryInterface::class,
            function (Application $app) {
                $config = InfluxDbConfigDto::getFromConfig();

                $client = new Client(
                    [
                        'url' => $config->url,
                        'token' => $config->token,
                        'bucket' => $config->bucket,
                        'org' => $config->organization,
                        'precision' => WritePrecision::NS,
                    ],
                );

                return new InfluxDbMonitoringRepository($client);
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
            MonitoringRepositoryInterface::class,
        ];
    }
}
