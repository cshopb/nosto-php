<?php

namespace App\Dtos\Apis;

use App\Dtos\CurrenciesExchangers\CurrencyExchangerApiConfigDto;
use Spatie\LaravelData\Data;

class ApiRequestOptionsDto extends Data
{
    public function __construct(
        public ApiRequestHeaderOptionsDto $headers = new ApiRequestHeaderOptionsDto(),
    )
    {
    }

    public static function fromApiConfig(CurrencyExchangerApiConfigDto $config): static
    {
        return new static(
            headers: new ApiRequestHeaderOptionsDto(
                authorization: $config->apiKey,
            ),
        );
    }
}
