<?php

namespace Tests\Unit\App\Dtos\Casters;

use App\Dtos\Casters\ToUpperCaseCast;
use Faker\Factory as Faker;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Tests\TestCase;

class ToUpperCaseCastTest extends TestCase
{
    private DataProperty $dataProperty;
    private CreationContext $creationContext;
    private ToUpperCaseCast $caster;
    private Generator $faker;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->faker = Faker::create();

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
