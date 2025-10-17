<?php

namespace Tests\Unit\App\Repositories;

use App\Dtos\Apis\ApiResponseHeadersDto;
use App\Dtos\Apis\Enums\ApiResponseConnectionEnum;
use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Exceptions\ApiCallException;
use App\Repositories\Apis\GuzzleApiRepository;
use DateTimeImmutable;
use Faker\Factory as Faker;
use Faker\Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class GuzzleApiRepositoryTest extends TestCase
{
    private Client $guzzleClient;
    private Generator $faker;

    /**
     * @throws ApiCallException
     */
    public function testGetFunctionWillReturnApiResponseDtoWithPropperData(): void
    {
        // Given
        $expectedHeaders = new ApiResponseHeadersDto(
            date: new DateTimeImmutable()->setTime(0, 0),
            contentType: ApiResponseContentTypeEnum::JSON,
            connection: ApiResponseConnectionEnum::KEEP_ALIVE,
        );

        $expectedContent = $this->faker->word();

        $guzzleResponseMock = new MockHandler(
            [
                new Response(
                    ApiResponseStatusCodeEnum::HTTP_OK->value,
                    $expectedHeaders->toArray(),
                    $expectedContent,
                ),
            ],
        );

        $handlerStack = HandlerStack::create($guzzleResponseMock);
        $client = new Client(['handler' => $handlerStack]);

        $repository = new GuzzleApiRepository($client);

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

    public function testGetFunctionWillThrowPropperExceptionWhenThereIsAnErrorWithResponse(): void
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
        $client = new Client(['handler' => $handlerStack]);

        $repository = new GuzzleApiRepository($client);

        $this->expectException(ApiCallException::class);

        // When
        $repository->get('some url');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
    }
}
