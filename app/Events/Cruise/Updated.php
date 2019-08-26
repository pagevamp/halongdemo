<?php

namespace App\Events\Cruise;

use App\Repos\Models\Cruise;
use Illuminate\Queue\SerializesModels;

class Updated implements PersistedInterface
{
    use SerializesModels;

    private $cruise;
    private $data;

    public function __construct(Cruise $cruise, array $data)
    {
        $this->cruise = $cruise;
        $this->data = $data;
    }

    public function getCruise(): Cruise
    {
        return $this->cruise;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
