<?php

namespace App\Repositories\Dbs;

use App\Exceptions\DbRepositoryException;
use App\Repositories\Dbs\Interfaces\DbRepositoryInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

/**
 * @template TData of Data
 * @template TModel of Model
 *
 * @mixin Eloquent
 */
class EloquentRepository implements DbRepositoryInterface
{
    private Builder $eloquentBuilder;

    /**
     * @param class-string<TData> $dtoClass
     * @param class-string<TModel> $modelClass
     */
    public function __construct(
        private readonly string $dtoClass,
        private readonly string $modelClass,
    )
    {
        $this->eloquentBuilder = new $this->modelClass()::query();
    }

    /**
     * @inheritDoc
     */
    public function first(array $columns = ['*']): Data
    {
        return $this->dtoClass::from($this->eloquentBuilder->get($columns));
    }

    /**
     * @inheritDoc
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->dtoClass::collect(
            $this->eloquentBuilder->get($columns),
        );
    }

    /**
     * @param string $method
     * @param array $parameters
     *
     * @return self<TData>
     *
     * @throws DbRepositoryException
     */
    public function __call(
        string $method,
        array $parameters,
    ): self
    {
        $this->eloquentBuilder = $this->eloquentBuilder->$method(...$parameters);

        return $this;
    }
}
