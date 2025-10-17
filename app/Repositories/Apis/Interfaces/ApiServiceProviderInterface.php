<?php

namespace App\Repositories\Apis\Interfaces;

use App\Dtos\Apis\ApiResponseDto;
use App\Exceptions\ApiCallException;

interface ApiServiceProviderInterface
{
    /**
     * Create and send an HTTP GET request.
     *
     * Use an absolute path to override the base path of the client,
     * or a relative path to append to the base path of the client.
     * The URL can contain the query string as well.
     *
     * @param string $url
     * @param array $options
     *
     * @return ApiResponseDto
     *
     * @throws ApiCallException
     */
    public function get(
        string $url,
        array $options = [],
    ): ApiResponseDto;
}
