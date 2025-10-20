<?php

namespace App\Helpers;

use JsonException;

class JsonHelper
{
    /**
     * @param string $data
     *
     * @return array
     * @throws JsonException
     */
    public function decode(string $data): array
    {
        return json_decode(
            json: $data,
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    /**
     * @param array $data
     *
     * @return string
     * @throws JsonException
     */
    public function encode(array $data): string
    {
        return json_encode(
            value: $data,
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
