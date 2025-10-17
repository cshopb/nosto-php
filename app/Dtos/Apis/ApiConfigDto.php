<?php

namespace App\Dtos\Apis;

use Spatie\LaravelData\Data;

class ApiConfigDto extends Data
{
    public function __construct(
        public ApiConfigUriDto $uri,
        public int $cache_requests_for_seconds = 60 * 60,
        public string $api_key = '',
    )
    {
    }
}
