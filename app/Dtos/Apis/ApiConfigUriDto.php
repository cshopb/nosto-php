<?php

namespace App\Dtos\Apis;

use Spatie\LaravelData\Data;

class ApiConfigUriDto extends Data
{
    public function __construct(
        public string $base,
        public string $singleRate,
        public string $availableCurrencies,
    )
    {
    }
}
