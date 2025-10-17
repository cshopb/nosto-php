<?php

namespace App\Repositories\Apis;

use App\Dtos\Apis\ApiResponseDto;
use App\Exceptions\ApiCallException;
use App\Repositories\Apis\Interfaces\ApiServiceProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

readonly class GuzzleApiRepository implements ApiServiceProviderInterface
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @inheritDoc
     */
    public function get(
        string $url,
        array $options = [],
    ): ApiResponseDto
    {
        try {
            $result = $this->client
                ->get(
                    $url,
                    $options,
                );
        } catch (GuzzleException $exception) {
            throw new ApiCallException(
                message: 'There was an error getting data from: ' . $url,
                previous: $exception,
            );
        }

        return ApiResponseDto::from($result);
    }
}
