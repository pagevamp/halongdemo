<?php

namespace App\Events\Cruise;

use App\Repos\Models\Cruise;

class ReviewsUpdated
{
    public $cruise;

    public function __construct(Cruise $cruise)
    {
        $this->cruise = $cruise;
    }
}
