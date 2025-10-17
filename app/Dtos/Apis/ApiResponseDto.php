<?php

namespace App\Dtos\Apis;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\Apis\Normalizers\GuzzleResponseNormalizer;
use Spatie\LaravelData\Data;

class ApiResponseDto extends Data
{
    public function __construct(
        public ApiResponseStatusCodeEnum $statusCode,
        public ApiResponseHeadersDto $headers,
        public string $content,
    )
    {
    }

    public static function normalizers(): array
    {
        return [
            GuzzleResponseNormalizer::class,
        ];
    }
}
