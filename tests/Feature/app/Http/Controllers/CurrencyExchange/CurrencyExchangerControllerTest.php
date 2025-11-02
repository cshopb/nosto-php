<?php

namespace Feature\app\Http\Controllers\CurrencyExchange;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyCollection;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Dtos\CurrenciesExchangers\CurrencyRateDto;
use App\Exceptions\CurrencyExchangerApiException;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use App\Repositories\Monitoring\Interface\MonitoringRepositoryInterface;
use DateTimeImmutable;
use Faker\Factory as Faker;
use Faker\Generator;
use Inertia\Testing\AssertableInertia as Assert;
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

        $this->mock(
            MonitoringRepositoryInterface::class,
            function (MockInterface $mock): void {
                $mock->expects('recordException')
                    ->once();
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

    public function testGetRateForCurrenciesWillReturnCorrectValueIfEverythingIsOk(): void
    {
        // Given
        $expectedReturn = CurrencyRateDto::from(
            [
                'baseCurrency' => $this->fakeCurrencyDto(),
                'quoteCurrency' => $this->fakeCurrencyDto(),
                'quote' => $this->faker->randomFloat(
                    4,
                    1,
                    10,
                ),
                'date' => new DateTimeImmutable(),
            ],
        );

        $this->mock(
            CurrencyExchangerInterface::class,
            function (MockInterface $mock) use ($expectedReturn): void {
                $mock->expects('getCurrencyFromCode')
                    ->times(4)
                    ->andReturn(
                        $expectedReturn->baseCurrency,
                        $expectedReturn->quoteCurrency,
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
                    ->twice()
                    ->andReturn($expectedReturn);
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
        $response->assertStatus(ApiResponseStatusCodeEnum::HTTP_OK->value);

        $response->assertInertia(
            static fn(Assert $page) => $page
                ->component('CurrencyExchanger/Index')
                ->has('availableCurrencies')
                ->has(
                    'currencyRate',
                    static fn(Assert $page) => $page
                        ->where(
                            'base_currency',
                            $expectedReturn->baseCurrency,
                        )
                        ->where(
                            'quote_currency',
                            $expectedReturn->quoteCurrency,
                        )
                        ->where(
                            'quote',
                            $expectedReturn->quote,
                        )
                        ->where(
                            'date',
                            $expectedReturn->date->format(DATE_ATOM),
                        ),
                )
                ->reloadOnly(
                    'currencyRate',
                    static fn(Assert $page) => $page
                        ->missing('availableCurrencies')
                        ->has(
                            'currencyRate',
                            static fn(Assert $page) => $page
                                ->where(
                                    'base_currency',
                                    $expectedReturn->baseCurrency,
                                )
                                ->where(
                                    'quote_currency',
                                    $expectedReturn->quoteCurrency,
                                )
                                ->where(
                                    'quote',
                                    $expectedReturn->quote,
                                )
                                ->where(
                                    'date',
                                    $expectedReturn->date->format(DATE_ATOM),
                                ),
                        ),
                ),
        );
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
