<?php

namespace App\Repos\Traits;

trait CanBeFeatured
{
    public function setFeaturedIndexAttribute($index)
    {
        if ($index) {
            $index = 10000 - $index;
        }
        $this->attributes['featured_index'] = $index;
    }

    public function getFeaturedIndexAttribute($index)
    {
        if ($index) {
            $index = 10000 - $index;
        }

        return $index;
    }

    public function scopeFeatured($query)
    {
        return $query->whereNotNull('featured_index');
    }

    public function scopeNotFeatured($query)
    {
        return $query->whereNull('featured_index');
    }
}
