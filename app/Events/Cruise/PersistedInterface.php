<?php

namespace App\Events\Cruise;

use App\Repos\Models\Cruise;

interface PersistedInterface
{
    public function getCruise(): Cruise;

    public function getData(): array;
}
