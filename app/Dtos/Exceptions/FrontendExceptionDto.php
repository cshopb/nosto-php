<?php

namespace App\Dtos\Exceptions;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use Spatie\LaravelData\Data;
use Throwable;

class FrontendExceptionDto extends Data
{
    public function __construct(
        public ApiResponseStatusCodeEnum $code,
        public string $message,
    )
    {
    }

    public static function fromException(Throwable $exception): FrontendExceptionDto
    {
        $code = $exception->getCode();
        if ($code === 0) {
            $code = ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR;
        }

        return self::from(
            [
                'code' => $code,
                'message' => $exception->getMessage(),
            ],
        );
    }
}
