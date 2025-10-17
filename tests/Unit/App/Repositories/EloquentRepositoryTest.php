<?php

namespace Tests\Unit\App\Repositories;

use App\Dtos\UserDto;
use App\Models\User;
use App\Repositories\Dbs\EloquentRepository;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Spatie\LaravelData\DataCollection;

class EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentRepository $repository;
    private Generator $faker;

    public function testFoo(): void
    {
        // Given
        $user = User::factory()->create();

        $expected = UserDto::collect(
            [$user],
            DataCollection::class,
        );

        // When
        $result = $this->repository->where(
            'email',
            $user->email,
        )->get();

        // Then
        $this->assertEquals(
            $expected,
            $result,
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();

        $this->repository = new EloquentRepository(
            UserDto::class,
            User::class,
        );
    }
}
