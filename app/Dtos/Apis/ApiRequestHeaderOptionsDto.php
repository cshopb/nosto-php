<?php

namespace App\Dtos\Apis;

use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\UpperCaseMapper;

#[MapOutputName(UpperCaseMapper::class)]
class ApiRequestHeaderOptionsDto extends Data
{
    public function __construct(
        public ApiResponseContentTypeEnum $accept = ApiResponseContentTypeEnum::JSON,
        public string $authorization = '',
    )
    {
    }
}
