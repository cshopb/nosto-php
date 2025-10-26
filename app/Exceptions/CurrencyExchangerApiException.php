<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CurrencyExchangerApiException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        private bool $isNotPremiumAccountException = false,
    )
    {
        parent::__construct($message, $code, $previous);
    }

    public function isNotPremiumAccountException(): bool
    {
        return $this->isNotPremiumAccountException;
    }
}
