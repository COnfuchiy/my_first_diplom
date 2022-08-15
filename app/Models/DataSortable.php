<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

interface DataSortable extends DataSearchable
{

    public static function validateFilterParams(array $requestParams): bool;

    public static function filter(Builder $query, string $sortColumn, string $sortType): Builder;

}
