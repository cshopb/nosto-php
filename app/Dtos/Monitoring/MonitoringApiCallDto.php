<?php

namespace App\Dtos\Monitoring;

use App\Dtos\Apis\ApiRequestOptionsDto;
use App\Repositories\Apis\Interfaces\ApiRepositoryInterface;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class MonitoringApiCallDto extends Data
{
    /**
     * @param class-string<ApiRepositoryInterface> $client
     * @param string $url
     * @param ApiRequestOptionsDto $options
     */
    public function __construct(
        public string $client,
        #[Url]
        public string $url,
        public ApiRequestOptionsDto $options,
    )
    {
    }
}
