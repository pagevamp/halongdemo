<?php

namespace App\Listeners\Cruise;

use App\Repos\Models\CruiseMeta;
use App\Events\Cruise\PersistedInterface as CruisePersistedEvent;

class UpdateMeta
{
    private $cruiseMeta;
    private $data;

    public function __construct(CruiseMeta $cruiseMeta)
    {
        $this->cruiseMeta = $cruiseMeta;
    }

    public function handle(CruisePersistedEvent $event)
    {
        $this->cruiseMeta->create($event->getCruise(), $event->getData());
    }
}
