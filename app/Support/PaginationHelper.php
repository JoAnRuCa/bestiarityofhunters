<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginationHelper
{
    public static function paginate(Collection $items, int $perPage = 20): LengthAwarePaginator
    {
        $page = request('page', 1);

        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        // Mantener parámetros de búsqueda
        $paginator->appends(request()->query());

        return $paginator;
    }
}
