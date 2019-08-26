<?php

namespace App\Observers;

use App\Repos\Models\Cruise;
use App\Repos\Models\Media;

class CruiseObserver
{
    /**
     * Handle the media "deleted" event.
     *
     * @param \App\Media $media
     */
    public function deleting(Cruise $cruise)
    {
        $cruise->experiences()->each(function ($item) {
            $item->delete();
        });
    }
}
