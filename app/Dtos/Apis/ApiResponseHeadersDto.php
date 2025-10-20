<?php

namespace App\Dtos\Apis;

use App\Dtos\Apis\Enums\ApiResponseConnectionEnum;
use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use DateTimeImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\KebabCaseMapper;

#[MapName(KebabCaseMapper::class)]
class ApiResponseHeadersDto extends Data
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?DateTimeImmutable $date = null,
        public ?ApiResponseContentTypeEnum $contentType = null,
        public ?ApiResponseConnectionEnum $connection = null,
    )
    {
    }
}
