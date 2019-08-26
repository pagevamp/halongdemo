<?php

namespace App\Events\Registrar;

use App\Events\Cruise\Created as CruiseCreated;
use App\Events\Cruise\ReviewsUpdated;
use App\Events\Cruise\Updated as CruiseUpdated;
use App\Listeners\Cruise\UpdateActivity;
use App\Listeners\Cruise\UpdateCategory;
use App\Listeners\Cruise\UpdateFacility;
use App\Listeners\Cruise\UpdateMeta;
use App\Listeners\Cruise\UpdateReviews;
use Illuminate\Support\Facades\Event;

class CruiseRegistrar
{
    public function register()
    {
        /*
         * CRUISE CREATED
         */
        Event::listen(CruiseCreated::class, UpdateMeta::class);

        /*
         * CRUISE UPDATED
         */
        Event::listen(CruiseUpdated::class, UpdateMeta::class);
    }
}
