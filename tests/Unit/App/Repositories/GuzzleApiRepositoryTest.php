<?php

namespace Tests\Unit\App\Repositories;

use App\Dtos\Apis\ApiResponseDto;
use App\Dtos\Apis\ApiResponseHeadersDto;
use App\Dtos\Apis\Enums\ApiResponseConnectionEnum;
use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Exceptions\ApiCallException;
use App\Helpers\JsonHelper;
use App\Repositories\Apis\GuzzleApiRepository;
use DateTimeImmutable;
use Faker\Factory as Faker;
use Faker\Generator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InfluxDB2\Client as InfluxDbClient;
use InfluxDB2\WriteApi;
use JsonException;
use Tests\TestCase;

class GuzzleApiRepositoryTest extends TestCase
{
    private Generator $faker;
    private JsonHelper $jsonHelper;
    private InfluxDbClient $influxDbClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
        $this->jsonHelper = new JsonHelper();

        $influxDbWriter = $this->createMock(WriteApi::class);
        $influxDbWriter->expects($this->once())
            ->method('write');

        $influxDbWriter->expects($this->once())
            ->method('close');

        $influxDbClient = $this->createMock(InfluxDbClient::class);
        $influxDbClient->expects($this->once())
            ->method('createWriteApi')
            ->willReturn($influxDbWriter);

        $this->influxDbClient = $influxDbClient;
    }

    /**
     * @throws ApiCallException|JsonException
     */
    public function testGetFunctionWillReturnApiResponseDtoWithPropperData(): void
    {
        // Given
        $expectedHeaders = new ApiResponseHeadersDto(
            date: new DateTimeImmutable()->setTime(0, 0),
            contentType: ApiResponseContentTypeEnum::JSON,
            connection: ApiResponseConnectionEnum::KEEP_ALIVE,
        );

        $expectedContent = [$this->faker->word() => $this->faker->word()];

        $guzzleResponseMock = new MockHandler(
            [
                new Response(
                    ApiResponseStatusCodeEnum::HTTP_OK->value,
                    $expectedHeaders->toArray(),
                    $this->jsonHelper->encode($expectedContent),
                ),
            ],
        );

        $handlerStack = HandlerStack::create($guzzleResponseMock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $repository = new GuzzleApiRepository(
            $guzzleClient,
            $this->influxDbClient,
        );

        // When
        $result = $repository->get('some url');

        // Then
        $this->assertEquals(
            $expectedHeaders->toArray(),
            $result->headers->toArray(),
        );

        $this->assertEquals(
            $expectedContent,
            $result->content,
        );
    }

    public function testGetFunctionWillThrowPropperExceptionWhenBadResponseExceptionIsNotTheExceptionThrownFromClient(): void
    {
        // Given
        $guzzleResponseMock = new MockHandler(
            [
                new RequestException(
                    'Error Communicating with Server',
                    new Request(
                        'GET',
                        'test',
                    ),
                ),
            ],
        );

        $handlerStack = HandlerStack::create($guzzleResponseMock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $repository = new GuzzleApiRepository(
            $guzzleClient,
            $this->influxDbClient,
        );

        $this->expectException(ApiCallException::class);

        // When
        $repository->get('some url');
    }

    public function testGetFunctionWillThrowPropperExceptionWhenBadResponseExceptionIsThrownFromClient(): void
    {
        // Given
        $expectedCode = $this->faker
            ->randomElement(ApiResponseStatusCodeEnum::cases())
            ->value;

        $expectedBody = $this->faker->word();
        $expectedHeaders = [
            ApiResponseContentTypeEnum::getHeaderName() => ApiResponseContentTypeEnum::TEXT->value,
        ];

        $expectedResponse = new Response(
            status: $expectedCode,
            headers: $expectedHeaders,
            body: $expectedBody,
        );

        $expectedRequest = new Request(
            'GET',
            'test',
        );

        $expectedUrl = $this->faker->word();

        $expectedResponseDto = ApiResponseDto::from(
            [
                'statusCode' => $expectedCode,
                'headers' => $expectedHeaders,
                'content' => $expectedBody,
            ],
        );

        $guzzleResponseMock = new MockHandler(
            [
                new BadResponseException(
                    message: 'Error Communicating with Server',
                    request: $expectedRequest,
                    response: $expectedResponse,
                ),
            ],
        );

        $handlerStack = HandlerStack::create($guzzleResponseMock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $repository = new GuzzleApiRepository(
            $guzzleClient,
            $this->influxDbClient,
        );

        $this->expectException(ApiCallException::class);
        $this->expectExceptionObject(
            new ApiCallException(
                message: "There was an error getting data from: {$expectedUrl}",
                code: $expectedCode,
            ),
        );

        // When
        try {
            $repository->get($expectedUrl);
        } catch (ApiCallException $exception) {
            // Then
            $this->assertEquals(
                $expectedRequest,
                $exception->getRequest(),
            );

            $this->assertEquals(
                $expectedResponseDto,
                $exception->getResponse(),
            );

            throw $exception;
        }
    }
}
