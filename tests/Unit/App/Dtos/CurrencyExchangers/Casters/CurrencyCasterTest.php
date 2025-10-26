<?php

namespace Tests\Unit\App\Dtos\CurrencyExchangers\Casters;

use App\Dtos\CurrenciesExchangers\Casters\CurrencyCaster;
use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Exceptions\CurrencyExchangerApiException;
use App\Exceptions\DataCasterException;
use App\Providers\RepositoryServiceProvider;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use Faker\Factory as Faker;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Tests\TestCase;

class CurrencyCasterTest extends TestCase
{
    private Generator $faker;
    private DataProperty $dataProperty;
    private CreationContext $creationContext;
    private CurrencyExchangerInterface|MockObject $exchanger;
    private CurrencyCaster $caster;

    /**
     * @throws DataCasterException
     */
    public function testCastWillReturnTheOriginalCurrencyDtoIfPassedAsValue(): void
    {
        // Given
        $expectedValue = $this->fakeCurrencyDto();

        // When
        $resultCast = $this->caster->cast(
            $this->dataProperty,
            $expectedValue,
            [],
            $this->creationContext,
        );

        $resultIteratorCast = $this->caster->castIterableItem(
            $this->dataProperty,
            $expectedValue,
            [],
            $this->creationContext,
        );

        // Then
        $this->assertEquals(
            $expectedValue,
            $resultCast,
        );

        $this->assertEquals(
            $expectedValue,
            $resultIteratorCast,
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

    /**
     * @throws DataCasterException
     */
    public function testCastWillReturnUncastableIfPassedValueIsNorStringAndNotCurrencyDto(): void
    {
        // Given
        $expectedValue = Uncastable::create();

        // When
        $resultCast = $this->caster->cast(
            $this->dataProperty,
            $this->faker->numberBetween(),
            [],
            $this->creationContext,
        );

        $resultIteratorCast = $this->caster->castIterableItem(
            $this->dataProperty,
            $this->faker->numberBetween(),
            [],
            $this->creationContext,
        );

        // Then
        $this->assertEquals(
            $expectedValue,
            $resultCast,
        );

        $this->assertEquals(
            $expectedValue,
            $resultIteratorCast,
        );
    }

    /**
     * @throws DataCasterException
     */
    public function testCastWillReturnUncastableIfPassedValueIsNorFoundInAvailableCurrencyList(): void
    {
        // Given
        $expectedValue = Uncastable::create();
        $value = $this->faker->word();

        $this->exchanger
            ->expects($this->exactly(2))
            ->method('getCurrencyFromCode')
            ->willReturn(null);

        $caster = new CurrencyCaster($this->exchanger);

        // When
        $resultCast = $caster->cast(
            $this->dataProperty,
            $value,
            [],
            $this->creationContext,
        );

        $resultIteratorCast = $caster->castIterableItem(
            $this->dataProperty,
            $value,
            [],
            $this->creationContext,
        );

        // Then
        $this->assertEquals(
            $expectedValue,
            $resultCast,
        );

        $this->assertEquals(
            $expectedValue,
            $resultIteratorCast,
        );
    }

    /**
     * @throws DataCasterException
     */
    public function testCastWillReturnCurrencyDtoWhenItIsFoundInAvailableCurrencyList(): void
    {
        // Given
        $expectedValue = $this->fakeCurrencyDto();
        $value = $this->faker->word();

        $this->exchanger
            ->expects($this->exactly(2))
            ->method('getCurrencyFromCode')
            ->willReturn($expectedValue);

        $caster = new CurrencyCaster($this->exchanger);

        // When
        $resultCast = $caster->cast(
            $this->dataProperty,
            $value,
            [],
            $this->creationContext,
        );

        $resultIteratorCast = $caster->castIterableItem(
            $this->dataProperty,
            $value,
            [],
            $this->creationContext,
        );

        // Then
        $this->assertEquals(
            $expectedValue,
            $resultCast,
        );

        $this->assertEquals(
            $expectedValue,
            $resultIteratorCast,
        );
    }

    public function testCastWillThrowExceptionIfThereIsAnErrorRetrievingCurrencyFromCurrencyAvailabilityList(): void
    {
        // Given
        $expectedValue = $this->fakeCurrencyDto();
        $value = $this->faker->word();

        $this->exchanger
            ->expects($this->once())
            ->method('getCurrencyFromCode')
            ->willThrowException(new CurrencyExchangerApiException());

        $caster = new CurrencyCaster($this->exchanger);

        // When
        $this->expectException(DataCasterException::class);

        $caster->cast(
            $this->dataProperty,
            $value,
            [],
            $this->creationContext,
        );
    }

    public function testCastIterableItemWillThrowExceptionIfThereIsAnErrorRetrievingCurrencyFromCurrencyAvailabilityList(): void
    {
        // Given
        $expectedValue = $this->fakeCurrencyDto();
        $value = $this->faker->word();

        $this->exchanger
            ->expects($this->once())
            ->method('getCurrencyFromCode')
            ->willThrowException(new CurrencyExchangerApiException());

        $caster = new CurrencyCaster($this->exchanger);

        // When
        $this->expectException(DataCasterException::class);

        $caster->castIterableItem(
            $this->dataProperty,
            $value,
            [],
            $this->creationContext,
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();

        $this->dataProperty = $this->getMockBuilder(DataProperty::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->creationContext = $this->getMockBuilder(CreationContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var CurrencyExchangerInterface|MockObject $exchanger */
        $exchanger = $this->getMockBuilder(RepositoryServiceProvider::$currencyExchanger)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exchanger = $exchanger;

        $this->caster = new CurrencyCaster($exchanger);
    }
}
