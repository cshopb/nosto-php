<?php

namespace App\Http\Controllers;

use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;

class TestController extends Controller
{
    public function __construct(private readonly CurrencyExchangerInterface $repository)
    {
    }

    public function test()
    {
        $f = $this->repository->getAvailableCurrencies();

        $t = 1;

        return $f;
    }

    public function foo()
    {
        $f = [
            'numericCode' => 0,
            'decimalDigits' => 0,
            'name' => '',
            'active' => '',
        ];

        $f = $this->repository->getRateForCurrency(
            CurrencyDto::from(['code' => 'EUR', ...$f]),
            CurrencyDto::from(['code' => 'USD', ...$f]),
        );

        $t = 1;

        return $f;
    }
}
