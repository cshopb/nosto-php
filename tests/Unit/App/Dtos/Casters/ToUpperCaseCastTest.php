<?php

namespace Tests\Unit\App\Dtos\Casters;

use App\Dtos\Casters\ToUpperCaseCast;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Tests\TestCase;

class ToUpperCaseCastTest extends TestCase
{
    use WithFaker;

    private DataProperty $dataProperty;
    private CreationContext $creationContext;
    private ToUpperCaseCast $caster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataProperty = $this->getMockBuilder(DataProperty::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->creationContext = $this->getMockBuilder(CreationContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->caster = new ToUpperCaseCast();
    }

    public function testCastFunctionWillReturnTheOriginalValueIfNotString(): void
    {
        // Given
        $expectedValue = [
            'not a string',
        ];

        // When
        $result = $this->caster
            ->cast(
                $this->dataProperty,
                $expectedValue,
                [],
                $this->creationContext,
            );

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    public function testCastFunctionWillReturnUppercaseValueOfString(): void
    {
        // Given
        $value = $this->faker->word();
        $expectedValue = strtoupper($value);

        // When
        $result = $this->caster
            ->cast(
                $this->dataProperty,
                $value,
                [],
                $this->creationContext,
            );

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }
}
