<?php

namespace App\Providers;

use App\Repositories\Apis\GuzzleApiRepository;
use App\Repositories\Apis\Interfaces\ApiServiceProviderInterface;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app
            ->bind(
                ApiServiceProviderInterface::class,
                function (Application $app) {
//
//                    $app->make('config')->get('api');
//                    $app->make('cache');

                    return new GuzzleApiRepository(new Client());
                },
            );
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ApiServiceProviderInterface::class,
        ];
    }
}
