<?php

namespace App\Dtos\Apis\Normalizers;

use GuzzleHttp\Psr7\Response;
use Spatie\LaravelData\Normalizers\Normalizer;

class GuzzleResponseNormalizer implements Normalizer
{

    public function normalize(mixed $value): ?array
    {
        if ($value instanceof Response === false) {
            return null;
        }

        return [
            'statusCode' => $value->getStatusCode(),
            'headers' => $this->normalizeHeaders($value),
            'content' => $value->getBody()->getContents(),
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
}
