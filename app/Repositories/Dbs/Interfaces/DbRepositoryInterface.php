<?php

namespace App\Repositories\Dbs\Interfaces;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * @template TData of Data
 */
interface DbRepositoryInterface
{
    /**
     * @param array $columns
     *
     * @return DataCollection<TData>
     */
    public function get(array $columns = ['*']): DataCollection;

    /**
     * @param array $columns
     *
     * @return Data<TData>
     */
    public function first(array $columns = ['*']): Data;
}
