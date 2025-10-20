<?php

namespace App\Providers;

use App\Helpers\JsonHelper;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app
            ->bind(
                JsonHelper::class,
                JsonHelper::class,
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
            JsonHelper::class,
        ];
    }
}
