<?php

namespace App\Dtos\Apis;

use App\Dtos\Apis\Enums\ApiResponseConnectionEnum;
use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use DateTimeImmutable;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class ApiResponseHeadersDto extends Data
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?DateTimeImmutable $date = null,
        #[MapInputName('content-type')]
        #[MapOutputName('content-type')]
        public ?ApiResponseContentTypeEnum $contentType = null,
        public ?ApiResponseConnectionEnum $connection = null,
    )
    {
    }
}
