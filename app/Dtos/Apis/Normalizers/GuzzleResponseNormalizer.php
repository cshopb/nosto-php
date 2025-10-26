<?php

namespace App\Dtos\Apis\Normalizers;

use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use App\Exceptions\DtoNormalizerException;
use App\Helpers\JsonHelper;
use GuzzleHttp\Psr7\Response;
use JsonException;
use Spatie\LaravelData\Normalizers\Normalizer;

readonly class GuzzleResponseNormalizer implements Normalizer
{
    public function __construct(private JsonHelper $jsonHelper)
    {
    }

    /**
     * @param mixed $value
     *
     * @return array|null
     * @throws DtoNormalizerException
     */
    public function normalize(mixed $value): ?array
    {
        if ($value instanceof Response === false) {
            return null;
        }

        $value = clone $value;

        $normalizedHeaders = $this->normalizeHeaders($value);
        $normalizedHeaders[ApiResponseContentTypeEnum::getHeaderName()] = $this->normalizeContentType(
            $normalizedHeaders[ApiResponseContentTypeEnum::getHeaderName()],
        );

        return [
            'status-code' => $value->getStatusCode(),
            'headers' => $normalizedHeaders,
            'content' => $this->normalizeContent(
                $normalizedHeaders[ApiResponseContentTypeEnum::getHeaderName()],
                $value->getBody()->getContents(),
            ),
        ];
    }

    private function normalizeHeaders(Response $response): array
    {
        $headers = $response->getHeaders();
        $result = [];
        foreach ($headers as $headerName => $headerValue) {
            if (count($headerValue) === 1) {
                $headerValue = $headerValue[0];
            }

            $result[strtolower($headerName)] = $headerValue;
        }

        return $result;
    }

    private function normalizeContentType(string $value): ApiResponseContentTypeEnum
    {
        return ApiResponseContentTypeEnum::tryFrom($value)
            ?? ApiResponseContentTypeEnum::TEXT;
    }

    /**
     * @param ApiResponseContentTypeEnum $contentType
     * @param string $content
     *
     * @return array|string
     * @throws DtoNormalizerException
     */
    private function normalizeContent(
        ApiResponseContentTypeEnum $contentType,
        string $content,
    ): array|string
    {
        try {
            return match ($contentType) {
                ApiResponseContentTypeEnum::JSON => $this->jsonHelper->decode($content),
                default => $content,
            };
        } catch (JsonException $exception) {
            throw new DtoNormalizerException(
                message: 'There was an error trying to convert the response content to JSON',
                previous: $exception,
            );
        }
    }
}
