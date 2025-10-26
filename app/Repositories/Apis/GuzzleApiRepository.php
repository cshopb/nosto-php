<?php

namespace App\Repositories\Apis;

use App\Dtos\Apis\ApiRequestOptionsDto;
use App\Dtos\Apis\ApiResponseDto;
use App\Exceptions\ApiCallException;
use App\Repositories\Apis\Interfaces\ApiRepositoryInterface;
use DateTimeImmutable;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use InfluxDB2\Client as InfluxDbClient;
use InfluxDB2\Point;
use InfluxDB2\WriteType;

readonly class GuzzleApiRepository implements ApiRepositoryInterface
{
    public function __construct(
        private GuzzleClient $guzzleClient,
        private InfluxDbClient $influxDbClient,
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
        $point = new Point('api_call')
            ->addTag(
                'client',
                'guzzle',
            )
            ->addField(
                'url',
                $url,
            )
            ->addField(
                'options',
                $options->toJson(),
            )
            ->time(new DateTimeImmutable());

        $this->writeToInfluxDb($point);

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

    private function writeToInfluxDb(Point $point): void
    {
        $writeInfluxDb = $this->influxDbClient
            ->createWriteApi(
                [
                    'writeType' => WriteType::SYNCHRONOUS,
                ],
            );

        $writeInfluxDb->write($point);
        $writeInfluxDb->close();
    }
}
