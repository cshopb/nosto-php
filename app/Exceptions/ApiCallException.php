<?php

namespace App\Exceptions;

use App\Dtos\Apis\ApiResponseDto;
use Exception;
use Throwable;

class ApiCallException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        private readonly mixed $request = null,
        private readonly ?ApiResponseDto $response = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }

    public function getRequest(): mixed
    {
        return $this->request;
    }

    public function getResponse(): ?ApiResponseDto
    {
        return $this->response;
    }
}
