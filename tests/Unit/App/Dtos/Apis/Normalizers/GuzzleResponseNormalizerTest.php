<?php

namespace Tests\Unit\App\Dtos\Apis\Normalizers;

use App\Dtos\Apis\Enums\ApiResponseConnectionEnum;
use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use App\Dtos\Apis\Normalizers\GuzzleResponseNormalizer;
use App\Exceptions\DtoNormalizerException;
use App\Helpers\JsonHelper;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\WithFaker;
use JsonException;
use Tests\TestCase;

class GuzzleResponseNormalizerTest extends TestCase
{
    use WithFaker;

    private GuzzleResponseNormalizer $normalizer;
    private JsonHelper $jsonHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonHelper = new JsonHelper();
        $this->normalizer = new GuzzleResponseNormalizer($this->jsonHelper);
    }

    /**
     * @throws DtoNormalizerException
     */
    public function testNormalizeFunctionWillReturnNullIfTheInputIsNotGuzzleResponse(): void
    {
        // Given
        $expectedValue = null;

        // When
        $result = $this->normalizer->normalize('some value');

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    /**
     * @throws DtoNormalizerException
     */
    public function testNormalizeFunctionWillReturnCorrectlyFormatedArray(): void
    {
        // Given
        $body = $this->faker->word();
        $date = $this->faker->dateTime()->format(DATE_RFC2822);

        $response = new Response(
            status: 200,
            headers: [
                'Date' => $date,
                'Content-Type' => ApiResponseContentTypeEnum::TEXT->value,
                'Connection' => ApiResponseConnectionEnum::KEEP_ALIVE->value,
            ],
            body: $body,
        );

        $expectedResult = [
            'status-code' => 200,
            'headers' => [
                'date' => $date,
                'content-type' => ApiResponseContentTypeEnum::TEXT,
                'connection' => ApiResponseConnectionEnum::KEEP_ALIVE->value,
            ],
            'content' => $body,
        ];

        // When
        $result = $this->normalizer->normalize($response);

        // Then
        $this->assertEquals(
            $expectedResult,
            $result,
        );
    }

    /**
     * @throws DtoNormalizerException|JsonException
     */
    public function testNormalizeFunctionWillCorrectlyDecodeJsonResponseBody(): void
    {
        // Given
        $expectedBody = [$this->faker->word() => $this->faker->word()];

        $response = new Response(
            status: 200,
            headers: [
                'Date' => $this->faker->dateTime()->format(DATE_RFC2822),
                'Content-Type' => ApiResponseContentTypeEnum::JSON->value,
                'Connection' => ApiResponseConnectionEnum::KEEP_ALIVE->value,
            ],
            body: $this->jsonHelper->encode($expectedBody),
        );

        // When
        $result = $this->normalizer->normalize($response);

        // Then
        $this->assertEquals(
            $expectedBody,
            $result['content'],
        );
    }

    /**
     * @throws DtoNormalizerException|JsonException
     */
    public function testNormalizeFunctionWillDefaultToTextAndNotDecodeAnythingIfContentTypeUnrecognised(): void
    {
        // Given
        $expectedBody = $this->jsonHelper->encode([$this->faker->word() => $this->faker->word()]);
        $date = $this->faker->dateTime()->format(DATE_RFC2822);

        $response = new Response(
            status: 200,
            headers: [
                'Date' => $date,
                'Content-Type' => $this->faker->word(),
                'Connection' => ApiResponseConnectionEnum::KEEP_ALIVE->value,
            ],
            body: $expectedBody,
        );

        $expectedResult = [
            'status-code' => 200,
            'headers' => [
                'date' => $date,
                'content-type' => ApiResponseContentTypeEnum::TEXT,
                'connection' => ApiResponseConnectionEnum::KEEP_ALIVE->value,
            ],
            'content' => $expectedBody,
        ];

        // When
        $result = $this->normalizer->normalize($response);

        // Then
        $this->assertEquals(
            $expectedResult,
            $result,
        );
    }

    /**
     * @throws DtoNormalizerException|JsonException
     */
    public function testNormalizeFunctionWillThrowAnExceptionIfContentIsMalformedJson(): void
    {
        // Given
        $response = new Response(
            status: 200,
            headers: [
                'Date' => $this->faker->dateTime()->format(DATE_RFC2822),
                'Content-Type' => ApiResponseContentTypeEnum::JSON->value,
                'Connection' => ApiResponseConnectionEnum::KEEP_ALIVE->value,
            ],
            body: '[',
        );

        $this->expectException(DtoNormalizerException::class);

        // When
        $result = $this->normalizer->normalize($response);
    }
}
