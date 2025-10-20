<?php

namespace App\Dtos\Apis\Enums;

enum ApiResponseContentTypeEnum: string
{
    case JSON = 'application/json';
    case TEXT = 'text/plain';

    public static function getHeaderName(): string
    {
        return 'content-type';
    }
}
