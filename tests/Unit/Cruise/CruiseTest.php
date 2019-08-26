<?php

namespace Tests\Unit\Cruise;

use App\Repos\Models\Cruise;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CruiseTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_cruise_has_many_metas()
    {
        $cruise = factory(Cruise::class)->create();
        $this->assertInstanceOf(Collection::class, $cruise->metas);
    }
}
