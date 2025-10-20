<?php

namespace App\Repositories\Dbs\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;

/**
 * @template TData of Data
 */
interface DbRepositoryInterface
{
    /**
     * @param array $columns
     *
     * @return Collection<TData>
     */
    public function get(array $columns = ['*']): Collection;

    /**
     * @param array $columns
     *
     * @return Data<TData>
     */
    public function first(array $columns = ['*']): Data;
}
