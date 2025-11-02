<?php

namespace Tests\Unit\App\Dtos\CurrencyExchangers\Casters;

use App\Dtos\CurrenciesExchangers\Casters\CurrencyUserInputSanitizeCaster;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Tests\TestCase;

class CurrencyUserInputSanitizeCasterTest extends TestCase
{
    use WithFaker;

    private DataProperty $dataProperty;
    private CreationContext $creationContext;
    private CurrencyUserInputSanitizeCaster $caster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataProperty = $this->getMockBuilder(DataProperty::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->creationContext = $this->getMockBuilder(CreationContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->caster = new CurrencyUserInputSanitizeCaster();
    }

    public function testCastWillSanitizeTheInput(): void
    {
        // Given
        $anchorValue = $this->faker->word();
        $scriptValue = $this->faker->word();

        $expectedValue = strtoupper($anchorValue . ' ' . $scriptValue);

        // When
        $resultCast = $this->caster
            ->cast(
                $this->dataProperty,
                "<a href='https://someurl.com'><b> $anchorValue </b></a><b onmouseover=alert('XSS!')>$scriptValue</b>",
                [],
                $this->creationContext,
            );

        // Then
        $this->assertEquals(
            $expectedValue,
            $resultCast,
        );
    }

    public function testCastWillReturnUncastableValueIfNotString(): void
    {
        // Given
        $value = $this->faker->randomDigit();

        // When
        $resultCast = $this->caster
            ->cast(
                $this->dataProperty,
                $value,
                [],
                $this->creationContext,
            );

        // Then
        $this->assertInstanceOf(
            Uncastable::class,
            $resultCast,
        );
    }
}
