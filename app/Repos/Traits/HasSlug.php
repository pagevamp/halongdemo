<?php

namespace App\Repos\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public function setSlugAttribute($slug)
    {
        if (empty($slug)) {
            $slug = $this->when($this->name, function () {
                return $this->name;
            }, $this->get('title'));
        }
        if ($slug) {
            $this->attributes['slug'] = Str::slug($slug);
        }
    }

    public function findBySlug($slug)
    {
        if (is_numeric($slug)) {
            return $this->where('id', $slug);
        }

        return $this->where('slug', $slug);
    }

    public function findBySlugOrFail($slug)
    {
        if (is_numeric($slug)) {
            return $this->where('id', $slug);
        }

        return $this->where('slug', $slug);
    }
}
