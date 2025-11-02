<?php

namespace App\Http\Controllers\CurrencyExchange;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyCollection;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Dtos\CurrenciesExchangers\CurrencyExchangerRequestDto;
use App\Dtos\CurrenciesExchangers\CurrencyRateDto;
use App\Exceptions\CurrencyExchangerApiException;
use App\Exceptions\CurrencyExchangerControllerException;
use App\Http\Controllers\Controller;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyExchangerController extends Controller
{
    public function __construct(private readonly CurrencyExchangerInterface $exchanger)
    {
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws CurrencyExchangerControllerException
     */
    public function __invoke(Request $request): Response
    {
        $requestDto = CurrencyExchangerRequestDto::fromRequest($request);

        $baseCurrencyDto = $this->getBaseCurrency($requestDto);
        $quotedCurrencyDto = $this->getQuoteCurrency($requestDto);

        return Inertia::render(
            'CurrencyExchanger/Index',
            [
                'availableCurrencies' => fn() => $this->getAvailableCurrencies(),
                'currencyRate' => fn() => $this->getRateForCurrencies(
                    $baseCurrencyDto,
                    $quotedCurrencyDto,
                ),
            ],
        );

    }

    /**
     * @param CurrencyExchangerRequestDto $requestDto
     *
     * @return CurrencyDto
     * @throws CurrencyExchangerControllerException
     */
    private function getBaseCurrency(CurrencyExchangerRequestDto $requestDto): CurrencyDto
    {
        try {
            $currency = $this->getCurrencyFromCode($requestDto->baseCurrency);
        } catch (CurrencyExchangerControllerException $exception) {
            if ($requestDto->isInertiaRequest === true) {
                throw $exception;
            }

            $currency = $this->getCurrencyFromCode($requestDto::DEFAULT_BASE_CURRENCY);
        }

        return $currency;
    }

    /**
     * @param CurrencyExchangerRequestDto $requestDto
     *
     * @return CurrencyDto
     * @throws CurrencyExchangerControllerException
     */
    private function getQuoteCurrency(CurrencyExchangerRequestDto $requestDto): CurrencyDto
    {
        try {
            $currency = $this->getCurrencyFromCode($requestDto->quoteCurrency);
        } catch (CurrencyExchangerControllerException $exception) {
            if ($requestDto->isInertiaRequest === true) {
                throw $exception;
            }

            $currency = $this->getCurrencyFromCode($requestDto::DEFAULT_QUOTE_CURRENCY);
        }

        return $currency;
    }

    /**
     * @param string $code
     *
     * @return CurrencyDto
     * @throws CurrencyExchangerControllerException
     */
    private function getCurrencyFromCode(string $code): CurrencyDto
    {
        try {
            $currency = $this->exchanger->getCurrencyFromCode($code);
        } catch (CurrencyExchangerApiException $exception) {
            $this->throwFinalExchangerException($exception);
        }

        if ($currency === null) {
            throw new CurrencyExchangerControllerException(
                message: "Currency $code doesn't exist",
                code: ApiResponseStatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY->value,
            );
        }

        return $currency;
    }

    /**
     * @return CurrencyCollection|null
     * @throws CurrencyExchangerControllerException
     */
    private function getAvailableCurrencies(): ?CurrencyCollection
    {
        try {
            return $this->exchanger->getAvailableCurrencies();
        } catch (CurrencyExchangerApiException $exception) {
            $this->throwFinalExchangerException($exception);
        }
    }

    /**
     * @param CurrencyDto $baseCurrencyDto
     * @param CurrencyDto $quotedCurrencyDto
     *
     * @return CurrencyRateDto|null
     * @throws CurrencyExchangerControllerException
     */
    private function getRateForCurrencies(
        CurrencyDto $baseCurrencyDto,
        CurrencyDto $quotedCurrencyDto,
    ): ?CurrencyRateDto
    {
        try {
            return $this->exchanger
                ->getRateForCurrencies(
                    $baseCurrencyDto,
                    $quotedCurrencyDto,
                );
        } catch (CurrencyExchangerApiException $exception) {
            $this->throwFinalExchangerException($exception);
        }
    }

    /**
     * @param CurrencyExchangerApiException $exception
     *
     * @return void
     * @throws CurrencyExchangerControllerException
     */
    private function throwFinalExchangerException(CurrencyExchangerApiException $exception): void
    {
        $message = 'There was an error grabbing data from the currency provider';
        $code = ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception->isNotPremiumAccountException() === true) {
            $message = 'Please upgrade your account';
            $code = ApiResponseStatusCodeEnum::HTTP_FORBIDDEN;
        }

        throw new CurrencyExchangerControllerException(
            message: $message,
            code: $code->value,
            previous: $exception,
        );
    }
}
