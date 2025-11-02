<?php

namespace App\Repositories\Apis;

use App\Dtos\Apis\ApiRequestOptionsDto;
use App\Dtos\Apis\ApiResponseDto;
use App\Dtos\Monitoring\MonitoringApiCallDto;
use App\Exceptions\ApiCallException;
use App\Repositories\Apis\Interfaces\ApiRepositoryInterface;
use App\Repositories\Monitoring\Interface\MonitoringRepositoryInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;

readonly class GuzzleApiRepository implements ApiRepositoryInterface
{
    public function __construct(
        private GuzzleClient $guzzleClient,
        private MonitoringRepositoryInterface $monitoring,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function get(
        string $url,
        ApiRequestOptionsDto $options = new ApiRequestOptionsDto(),
    ): ApiResponseDto
    {
        $monitoringData = new MonitoringApiCallDto(
            client: $this::class,
            url: $url,
            options: $options,
        );

        $this->monitoring
            ->recordApiCall($monitoringData);

        try {
            $result = $this->guzzleClient
                ->get(
                    $url,
                    $options->toArray(),
                );
        } catch (BadResponseException $exception) {
            throw new ApiCallException(
                message: 'There was an error getting data from: ' . $url,
                code: $exception->getCode(),
                previous: $exception,
                request: $exception->getRequest(),
                response: ApiResponseDto::from($exception->getResponse()),
            );
        } catch (GuzzleException $exception) {
            throw new ApiCallException(
                message: 'There was an error getting data from: ' . $url,
                code: $exception->getCode(),
                previous: $exception,
            );
        }

        return ApiResponseDto::from($result);
    }
}
