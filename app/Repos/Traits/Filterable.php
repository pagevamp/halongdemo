<?php

namespace App\Repos\Traits;

use App\Services\Filters\Filters;

trait Filterable
{
    public function scopeFilter($query, Filters $filters)
    {
        return $filters->apply($query);
    }

    public function scopeFindByIds($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }
}
