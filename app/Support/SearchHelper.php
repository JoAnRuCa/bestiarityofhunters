<?php

namespace App\Support;

use Illuminate\Support\Collection;

class SearchHelper
{
    public static function text(Collection $items, string $field, string $query): Collection
    {
        $q = strtolower($query);

        return $items->filter(fn($item) =>
            isset($item[$field]) &&
            str_contains(strtolower($item[$field]), $q)
        );
    }
}
