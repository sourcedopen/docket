<?php

namespace App\QueryBuilders\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class NullsLastSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->orderByRaw("{$property} IS NULL, {$property} {$direction}");
    }
}
