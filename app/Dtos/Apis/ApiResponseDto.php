<?php

namespace App\Dtos\Apis;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\Apis\Normalizers\GuzzleResponseNormalizer;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\KebabCaseMapper;
use Spatie\LaravelData\Normalizers\ArrayNormalizer;

#[MapName(KebabCaseMapper::class)]
class ApiResponseDto extends Data
{
    public function __construct(
        public ApiResponseStatusCodeEnum $statusCode,
        public ApiResponseHeadersDto $headers,
        public array|string $content,
    )
    {
    }

    public static function normalizers(): array
    {
        return [
            GuzzleResponseNormalizer::class,
            ArrayNormalizer::class,
        ];
    }
}
