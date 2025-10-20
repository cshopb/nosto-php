<?php

namespace Tests\Unit\App\Helpers;

use App\Helpers\JsonHelper;
use Faker\Factory as Faker;
use Faker\Generator;
use JsonException;
use Tests\TestCase;

class JsonHelperTest extends TestCase
{
    private JsonHelper $jsonHelper;
    private Generator $faker;

    /**
     * @throws JsonException
     */
    public function testDecodeFunctionWillReturnTheCorrectValue(): void
    {
        // Given
        $expectedValue = [$this->faker->word() => $this->faker->word()];

        // When
        $result = $this->jsonHelper
            ->decode(
                json_encode(
                    $expectedValue,
                    JSON_THROW_ON_ERROR,
                ),
            );

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    public function testDecodeFunctionWillThrowAnExceptionIfThereIsAnError(): void
    {
        // Given
        $this->expectException(JsonException::class);

        // When
        $this->jsonHelper->decode('some string that is not JSON');
    }

    /**
     * @throws JsonException
     */
    public function testEncodeFunctionWillReturnTheCorrectValue(): void
    {
        // Given
        $data = [$this->faker->word() => $this->faker->word()];
        $expectedValue = json_encode(
            $data,
            JSON_THROW_ON_ERROR,
        );

        // When
        $result = $this->jsonHelper
            ->encode($data);

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
        $this->jsonHelper = new JsonHelper();
    }
}
