<?php

namespace Feature\app\Http\Controllers\CurrencyExchange;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyCollection;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Exceptions\CurrencyExchangerApiException;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use Faker\Factory as Faker;
use Faker\Generator;
use Mockery\MockInterface;
use Tests\TestCase;

class CurrencyExchangerControllerTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
    }

    public function testRequestWillThrowUnprocessableEntityExceptionIfBaseCurrencyReturnsNull(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('getCurrencyFromCode')
                    ->twice()
                    ->andReturn(
                        null,
                        $this->fakeCurrencyDto(),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY->value);
    }

    public function testRequestWillThrowInternalServerErrorEntityExceptionIfErrorGettingBaseCurrency(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                /** @var ApiResponseStatusCodeEnum $exceptionCode */
                $exceptionCode = $this->faker
                    ->randomElement(
                        ApiResponseStatusCodeEnum::cases(),
                    );

                $mock->expects('getCurrencyFromCode')
                    ->once()
                    ->andThrow(
                        new CurrencyExchangerApiException(code: $exceptionCode->value),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value);
    }

    public function testRequestWillThrowUnprocessableEntityExceptionIfQuoteCurrencyReturnsNull(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('getCurrencyFromCode')
                    ->twice()
                    ->andReturn(
                        $this->fakeCurrencyDto(),
                        null,
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'quoteCurrency' => 'QCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY->value);
    }

    public function testRequestWillThrowInternalServerErrorEntityExceptionIfErrorGettingQuoteCurrency(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                /** @var ApiResponseStatusCodeEnum $exceptionCode */
                $exceptionCode = $this->faker
                    ->randomElement(
                        ApiResponseStatusCodeEnum::cases(),
                    );

                $mock->expects('getCurrencyFromCode')
                    ->once()
                    ->andThrow(
                        new CurrencyExchangerApiException(code: $exceptionCode->value),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value);
    }

    public function testGetAvailableCurrenciesWillThrowCorrectExceptionIfThereIsNotPremiumIssueErrorReturnedFromApi(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('getCurrencyFromCode')
                    ->twice()
                    ->andReturn(
                        $this->fakeCurrencyDto(),
                        $this->fakeCurrencyDto(),
                    );

                /** @var ApiResponseStatusCodeEnum $exceptionCode */
                $exceptionCode = $this->faker
                    ->randomElement(
                        ApiResponseStatusCodeEnum::cases(),
                    );

                $mock->expects('getAvailableCurrencies')
                    ->once()
                    ->andThrow(
                        new CurrencyExchangerApiException(code: $exceptionCode->value),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
                'quoteCurrency' => 'QCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value);
    }

    public function testGetAvailableCurrenciesWillThrowCorrectExceptionIfThereIsPremiumIssueErrorReturnedFromApi(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('getCurrencyFromCode')
                    ->twice()
                    ->andReturn(
                        $this->fakeCurrencyDto(),
                        $this->fakeCurrencyDto(),
                    );

                /** @var ApiResponseStatusCodeEnum $exceptionCode */
                $exceptionCode = $this->faker
                    ->randomElement(
                        ApiResponseStatusCodeEnum::cases(),
                    );

                $mock->expects('getAvailableCurrencies')
                    ->once()
                    ->andThrow(
                        new CurrencyExchangerApiException(
                            code: $exceptionCode->value,
                            isNotPremiumAccountException: true,
                        ),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
                'quoteCurrency' => 'QCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_FORBIDDEN->value);
    }

    public function testGetRateForCurrenciesWillThrowCorrectExceptionIfThereIsNotPremiumIssueErrorReturnedFromApi(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('getCurrencyFromCode')
                    ->twice()
                    ->andReturn(
                        $this->fakeCurrencyDto(),
                        $this->fakeCurrencyDto(),
                    );

                /** @var ApiResponseStatusCodeEnum $exceptionCode */
                $exceptionCode = $this->faker
                    ->randomElement(
                        ApiResponseStatusCodeEnum::cases(),
                    );

                $mock->expects('getAvailableCurrencies')
                    ->once()
                    ->andReturn(CurrencyCollection::make([]));

                $mock->expects('getRateForCurrencies')
                    ->once()
                    ->andThrow(
                        new CurrencyExchangerApiException(code: $exceptionCode->value),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
                'quoteCurrency' => 'QCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value);
    }

    public function testGetRateForCurrenciesWillThrowCorrectExceptionIfThereIsPremiumIssueErrorReturnedFromApi(): void
    {
        // Given
        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('getCurrencyFromCode')
                    ->twice()
                    ->andReturn(
                        $this->fakeCurrencyDto(),
                        $this->fakeCurrencyDto(),
                    );

                /** @var ApiResponseStatusCodeEnum $exceptionCode */
                $exceptionCode = $this->faker
                    ->randomElement(
                        ApiResponseStatusCodeEnum::cases(),
                    );

                $mock->expects('getAvailableCurrencies')
                    ->once()
                    ->andReturn(CurrencyCollection::make([]));

                $mock->expects('getRateForCurrencies')
                    ->once()
                    ->andThrow(
                        new CurrencyExchangerApiException(
                            code: $exceptionCode->value,
                            isNotPremiumAccountException: true,
                        ),
                    );
            },
        );

        $route = route(
            'exchange',
            [
                'baseCurrency' => 'BCC',
                'quoteCurrency' => 'QCC',
            ],
        );

        // When
        $response = $this->get($route);

        // Then
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_FORBIDDEN->value);
    }

    private function fakeCurrencyDto(): CurrencyDto
    {
        return CurrencyDto::from(
            [
                'code' => $this->faker->word(),
                'numericCode' => $this->faker->numberBetween(100, 999),
                'decimalDigits' => $this->faker->numberBetween(2, 5),
                'name' => $this->faker->word(),
                'active' => $this->faker->boolean(),
            ],
        );
    }
}
