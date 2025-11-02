<?php

namespace Tests\Unit\App\Repositories\Dbs;

use App\Dtos\UserDto;
use App\Models\User;
use App\Repositories\Dbs\EloquentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    private EloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentRepository(
            UserDto::class,
            User::class,
        );
    }

    public function testGetFunctionWillReturnTheCorrectDataWhenCalledWithChainedConstraint(): void
    {
        // Given
        $user = User::factory()->create();

        $expected = UserDto::collect(new Collection([$user]));

        // When
        /** @noinspection StaticInvocationViaThisInspection */
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

    public function testFirstFunctionWillReturnTheCorrectDataWhenCalledWithChainedConstraint(): void
    {
        // Given
        $user = User::factory()->create();
        User::factory()->create();

        $expected = UserDto::from($user);

        // When
        /** @noinspection StaticInvocationViaThisInspection */
        $result = $this->repository->where(
            'email',
            $user->email,
        )->first();

        // Then
        $this->assertEquals(
            $expected,
            $result,
        );
    }
}
