<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

interface DataSearchable
{

    public static function validateSearchParams(array $requestParams): bool;

    public static function search(Builder $query, array $searchRequest): Builder;

}
