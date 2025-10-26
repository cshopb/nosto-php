<?php

namespace Tests\Unit\App\Support;

use App\Support\LaravelViteNonceGenerator;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class LaravelViteNonceGeneratorTest extends TestCase
{
    public function testGenerateFunctionWillReturnAStringFromViteCspNonceGenerator(): void
    {
        // Given
        $expectedValue = 'Some String';

        Vite::shouldReceive('cspNonce')
            ->once()
            ->andReturn($expectedValue);

        $generator = new LaravelViteNonceGenerator();

        // When
        $result = $generator->generate();

        // Then
        $this->assertEquals(
            $expectedValue,
            $result,
        );
    }
}
