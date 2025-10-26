<?php

namespace Tests\Unit\App\Dtos\Apis\Enums;

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use Tests\TestCase;

class ApiResponseStatusCodeEnumTest extends TestCase
{
    public function testIsInformationalResponseWillReturnCorrectValue(): void
    {
        // Given
        $enum = ApiResponseStatusCodeEnum::HTTP_CONTINUE;
        $badEnum = ApiResponseStatusCodeEnum::HTTP_FORBIDDEN;

        // When
        $result = $enum->isInformationalResponse();
        $badResult = $badEnum->isInformationalResponse();

        // Then
        $this->assertTrue($result);
        $this->assertFalse($badResult);
    }

    public function testIsSuccessWillReturnCorrectValue(): void
    {
        // Given
        $enum = ApiResponseStatusCodeEnum::HTTP_OK;
        $badEnum = ApiResponseStatusCodeEnum::HTTP_FORBIDDEN;

        // When
        $result = $enum->isSuccess();
        $badResult = $badEnum->isSuccess();

        // Then
        $this->assertTrue($result);
        $this->assertFalse($badResult);
    }

    public function testIsRedirectionWillReturnCorrectValue(): void
    {
        // Given
        $enum = ApiResponseStatusCodeEnum::HTTP_MULTIPLE_CHOICES;
        $badEnum = ApiResponseStatusCodeEnum::HTTP_FORBIDDEN;

        // When
        $result = $enum->isRedirection();
        $badResult = $badEnum->isRedirection();

        // Then
        $this->assertTrue($result);
        $this->assertFalse($badResult);
    }

    public function testIsClientErrorWillReturnCorrectValue(): void
    {
        // Given
        $enum = ApiResponseStatusCodeEnum::HTTP_BAD_REQUEST;
        $badEnum = ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR;

        // When
        $result = $enum->isClientError();
        $badResult = $badEnum->isClientError();

        // Then
        $this->assertTrue($result);
        $this->assertFalse($badResult);
    }

    public function testIsServerErrorWillReturnCorrectValue(): void
    {
        // Given
        $enum = ApiResponseStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR;
        $badEnum = ApiResponseStatusCodeEnum::HTTP_FORBIDDEN;

        // When
        $result = $enum->isServerError();
        $badResult = $badEnum->isServerError();

        // Then
        $this->assertTrue($result);
        $this->assertFalse($badResult);
    }
}
