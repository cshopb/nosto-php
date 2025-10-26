<?php

namespace Tests\Unit\App\Repositories\CurrencyExchangers;

use App\Dtos\Apis\ApiRequestOptionsDto;
use App\Dtos\Apis\ApiResponseDto;
use App\Dtos\Apis\Enums\ApiResponseContentTypeEnum;
use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyCollection;
use App\Dtos\CurrenciesExchangers\Collections\CurrencyRateCollection;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Dtos\CurrenciesExchangers\CurrencyExchangerApiConfigDto;
use App\Dtos\CurrenciesExchangers\CurrencyRateDto;
use App\Exceptions\ApiCallException;
use App\Exceptions\CurrencyExchangerApiException;
use App\Repositories\Apis\GuzzleApiRepository;
use App\Repositories\Apis\Interfaces\ApiRepositoryInterface;
use App\Repositories\CurrencyExchangers\SwopCxCurrencyExchanger;
use Closure;
use DateTimeImmutable;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class SwopCxCurrencyExchangerTest extends TestCase
{
    private Generator|MockObject $faker;
    private ApiRepositoryInterface|MockObject $api;
    private CurrencyExchangerApiConfigDto $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();

        $this->api = $this->getMockBuilder(GuzzleApiRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = $this->fakeConfig();
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetAvailableCurrenciesFunctionWillGrabDataFromCacheIfItExistsThere(): void
    {
        // Given
        $expectedValue = CurrencyDto::collect(
            [
                $this->fakeCurrencyDto(),
                $this->fakeCurrencyDto(),
            ],
            CurrencyCollection::class,
        );


        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        Cache::expects('remember')
            ->once()
            ->with(
                $exchanger->getAvailableCurrencyCacheName(),
                $this->config->cacheRequestsForSeconds,
                Closure::class,
            )
            ->andReturn($expectedValue);

        // When
        $result = $exchanger->getAvailableCurrencies();

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetAvailableCurrenciesFunctionWillGrabDataFromApiIfItIsNotInCache(): void
    {
        // Given
        $response = [
            $this->fakeCurrencyDto(),
            $this->fakeCurrencyDto(),
        ];

        /** @var CurrencyCollection $expectedValue */
        $expectedValue = CurrencyDto::collect(
            $response,
            CurrencyCollection::class,
        );

        $expectedValue = $expectedValue->keyByCode();

        $this->api
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->config->uri->availableCurrencies,
                ApiRequestOptionsDto::fromApiConfig($this->config),
            )
            ->willReturn($this->fakeApiResponseDto($response));

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        // When
        $result = $exchanger->getAvailableCurrencies();

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    public function testGetAvailableCurrenciesFunctionWillThrowAnExceptionIfThereIsAnExceptionThrownByApiCall(): void
    {
        // Given
        $this->api
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->config->uri->availableCurrencies,
                ApiRequestOptionsDto::fromApiConfig($this->config),
            )
            ->willThrowException(new ApiCallException());

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        $this->expectException(CurrencyExchangerApiException::class);

        // When
        $exchanger->getAvailableCurrencies();
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetCurrencyFromCodeFunctionWillReturnTheCorrectValueIfExists(): void
    {
        // Given
        $expectedValue = $this->fakeCurrencyDto();

        /** @var CurrencyCollection $currencyCollection */
        $currencyCollection = CurrencyDto::collect(
            [
                $this->fakeCurrencyDto(),
                $expectedValue,
                $this->fakeCurrencyDto(),
            ],
            CurrencyCollection::class,
        );

        $currencyCollection = $currencyCollection->keyByCode();

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        Cache::expects('remember')
            ->once()
            ->with(
                $exchanger->getAvailableCurrencyCacheName(),
                $this->config->cacheRequestsForSeconds,
                Closure::class,
            )
            ->andReturn($currencyCollection);

        // When
        $result = $exchanger->getCurrencyFromCode($expectedValue->code);

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetCurrencyFromCodeFunctionWillReturnNullIfNoDataFound(): void
    {
        // Given
        $expectedValue = null;

        /** @var CurrencyCollection $currencyCollection */
        $currencyCollection = CurrencyDto::collect(
            [
                $this->fakeCurrencyDto(),
                $this->fakeCurrencyDto(),
            ],
            CurrencyCollection::class,
        );

        $currencyCollection = $currencyCollection->keyByCode();

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        Cache::expects('remember')
            ->once()
            ->with(
                $exchanger->getAvailableCurrencyCacheName(),
                $this->config->cacheRequestsForSeconds,
                Closure::class,
            )
            ->andReturn($currencyCollection);

        // When
        $result = $exchanger->getCurrencyFromCode('some none existing currency code');

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetRateForCurrencyFunctionWillGrabDataFromCacheIfItExistsThere(): void
    {
        // Given
        $baseCurrency = $this->fakeCurrencyDto();
        $expectedCurrencyRate = $this->fakeCurrencyRateDto($baseCurrency);

        /** @var CurrencyRateCollection $currencyRateCollection */
        $currencyRateCollection = CurrencyRateDto::collect(
            [
                $this->fakeCurrencyRateDto($baseCurrency),
                $expectedCurrencyRate,
                $this->fakeCurrencyRateDto($baseCurrency),
            ],
            CurrencyRateCollection::class,
        );

        $currencyRateCollection = $currencyRateCollection->keyByQuotedCurrency();


        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        Cache::expects('remember')
            ->once()
            ->with(
                $exchanger->getCurrencyRateCacheName($baseCurrency->code),
                $this->config->cacheRequestsForSeconds,
                Closure::class,
            )
            ->andReturn($currencyRateCollection);

        // When
        $result = $exchanger->getRateForCurrencies(
            $baseCurrency,
            $expectedCurrencyRate->quoteCurrency,
        );

        // Then
        $this->assertEquals(
            $expectedCurrencyRate,
            $result,
        );
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetRateForCurrencyFunctionWillGrabDataFromApiIfItIsNotInCache(): void
    {
        // Given
        $date = DateTimeImmutable::createFromMutable($this->faker->dateTime());
        $baseCurrency = $this->fakeCurrencyDto();
        $expectedCurrencyRate = $this->fakeCurrencyRateDto($baseCurrency);

        $response = [
            $this->fakeCurrencyRateDto(),
            $expectedCurrencyRate,
            $this->fakeCurrencyRateDto(),
        ];

        $this->api
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->config->uri->getListOfRatesUri(
                    $baseCurrency,
                    $date,
                ),
                ApiRequestOptionsDto::fromApiConfig($this->config),
            )
            ->willReturn($this->fakeApiResponseDto($response));

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        // When
        $result = $exchanger->getRateForCurrencies(
            $baseCurrency,
            $expectedCurrencyRate->quoteCurrency,
            $date,
        );

        // Then
        $this->assertEquals(
            $expectedCurrencyRate,
            $result,
        );
    }

    /**
     * @throws CurrencyExchangerApiException
     */
    public function testGetRateForCurrencyFunctionWillReturnNullIfNoDataFound(): void
    {
        // Given
        $expectedValue = null;
        $baseCurrency = $this->fakeCurrencyDto();

        /** @var CurrencyRateCollection $currencyCollection */
        $currencyCollection = CurrencyRateDto::collect(
            [
                $this->fakeCurrencyRateDto(),
                $this->fakeCurrencyRateDto(),
            ],
            CurrencyRateCollection::class,
        );

        $currencyCollection = $currencyCollection->keyByQuotedCurrency();

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        Cache::expects('remember')
            ->once()
            ->with(
                $exchanger->getCurrencyRateCacheName($baseCurrency->code),
                $this->config->cacheRequestsForSeconds,
                Closure::class,
            )
            ->andReturn($currencyCollection);

        // When
        $result = $exchanger->getRateForCurrencies(
            $baseCurrency,
            $this->fakeCurrencyDto(),
        );

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    public function testGetRateForCurrencyFunctionWillThrowAnExceptionWithGenericMessageIfThereIsAnExceptionThrownByApiCall(): void
    {
        // Given
        $date = DateTimeImmutable::createFromMutable($this->faker->dateTime());
        $baseCurrency = $this->fakeCurrencyDto();

        $this->api
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->config->uri->getListOfRatesUri(
                    $baseCurrency,
                    $date,
                ),
                ApiRequestOptionsDto::fromApiConfig($this->config),
            )
            ->willThrowException(new ApiCallException());

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        $this->expectException(CurrencyExchangerApiException::class);
        $this->expectExceptionMessage("Error grabbing single rate for {$baseCurrency->code} from swop-cx");

        // When
        $exchanger->getRateForCurrencies(
            $baseCurrency,
            $this->fakeCurrencyDto(),
            $date,
        );
    }

    public function testGetRateForCurrencyFunctionWillThrowAnExceptionWithPremiumMessageIfThereIsAnExceptionThrownByApiCall(): void
    {
        // Given
        $date = DateTimeImmutable::createFromMutable($this->faker->dateTime());
        $baseCurrency = $this->fakeCurrencyDto();

        $responseDto = ApiResponseDto::from(
            [
                'statusCode' => ApiResponseStatusCodeEnum::HTTP_UNAUTHORIZED,
                'headers' => [],
                'content' => [
                    'error' => [
                        'type' => 'feature_authorization',
                    ],
                ],
            ],
        );

        $this->api
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->config->uri->getListOfRatesUri(
                    $baseCurrency,
                    $date,
                ),
                ApiRequestOptionsDto::fromApiConfig($this->config),
            )
            ->willThrowException(new ApiCallException(response: $responseDto));

        $exchanger = new SwopCxCurrencyExchanger(
            $this->api,
            $this->config,
        );

        $this->expectException(CurrencyExchangerApiException::class);
        $this->expectExceptionMessage(
            "Please upgrade the account for SWOP-CX provider to get the rate for {$baseCurrency->code}",
        );

        // When
        $exchanger->getRateForCurrencies(
            $baseCurrency,
            $this->fakeCurrencyDto(),
            $date,
        );
    }

    private function fakeConfig(): CurrencyExchangerApiConfigDto
    {
        return CurrencyExchangerApiConfigDto::from(
            [
                'uri' => [
                    'base' => $this->faker->word(),
                    'listOfRates' => $this->faker->word(),
                    'availableCurrencies' => $this->faker->word(),
                ],
            ],
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

    private function fakeApiResponseDto(array|string $content = ''): ApiResponseDto
    {
        return ApiResponseDto::from(
            [
                'statusCode' => ApiResponseStatusCodeEnum::HTTP_OK->value,
                'headers' => [
                    'contentType' => ApiResponseContentTypeEnum::JSON->value,
                ],
                'content' => $content,
            ],
        );
    }

    private function fakeCurrencyRateDto(?CurrencyDto $baseCurrency = null): CurrencyRateDto
    {
        return CurrencyRateDto::from(
            [
                'baseCurrency' => $baseCurrency ?? $this->fakeCurrencyDto(),
                'quoteCurrency' => $this->fakeCurrencyDto(),
                'quote' => $this->faker->randomFloat(6),
                'date' => $this->faker->dateTime(),
            ],
        );
    }
}
