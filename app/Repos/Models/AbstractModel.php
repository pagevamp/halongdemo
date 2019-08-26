<?php

namespace App\Repos\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractModel extends Model
{
    public function getId()
    {
        return $this->id;
    }

    public function scopeByIds($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }
}
