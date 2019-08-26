<?php

namespace Tests\Feature\Cruise;

use App\Repos\Models\Cruise;
use App\Repos\Models\CruiseActivity;
use App\Repos\Models\CruiseCategory;
use App\Repos\Models\CruiseFacility;
use App\Repos\Models\CruiseMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CruiseCreateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_cruise_can_be_created_by_admin_only()
    {
        $this->signInAs('admin');

        $params = factory(Cruise::class)->states(
            'with_route',
            'with_post_metas',
            'with_categories',
            'with_activities',
            'with_facilities'
        )->make();

        $response = $this->postJson(route('cruises.create'), $params->toArray())->assertStatus(201);
        $response = json_decode($response->getContent(), 1);
        $cruiseId = $response['data']['id'];
        $params->only($params->getFillable());
        $this->assertDatabaseHas('cruises', [
            'name' => $params->name,
            'price' => $params->price,
            'published' => $params->published,
            'featured_index' => 10000 - $params->featured_index,
            'short_description' => $params->short_description,
            'long_description' => $params->long_description,
            'video' => $params->video,
            'slug' => $params->slug,
            'route_id' => $params->route_id,
            'star' => $params->star,
            'total_average_rating' => $params->total_average_rating,
        ]);

        $metas = array_intersect_key($params->toArray(), array_flip(CruiseMeta::getAllowedKeys()));

        foreach ($metas as $name => $value) {
            $this->assertDatabaseHas('cruise_metas', [
                'name' => $name,
                'value' => $value,
                'cruise_id' => $cruiseId,
            ]);
        }

        foreach ($params['category_ids'] as $categoryId) {
            $this->assertDatabaseHas('cruise_categories', [
                'category_id' => $categoryId,
                'cruise_id' => $cruiseId,
            ]);
        }

        $this->assertCount(\count($params['category_ids']), CruiseCategory::all());

        foreach ($params['activity_ids'] as $activityId) {
            $this->assertDatabaseHas('cruise_activities', [
                'activity_id' => $activityId,
                'cruise_id' => $cruiseId,
            ]);
        }
        $this->assertCount(\count($params['activity_ids']), CruiseActivity::all());

        foreach ($params['facility_ids'] as $facilityId) {
            $this->assertDatabaseHas('cruise_facilities', [
                'facility_id' => $facilityId,
                'cruise_id' => $cruiseId,
            ]);
        }
        $this->assertCount(\count($params['facility_ids']), CruiseFacility::all());
    }

    public function test_guest_may_not_create_cruise()
    {
        $this->postJson(route('cruises.create'), [])->assertStatus(401);
    }

    public function test_cruise_cannot_be_created_by_client()
    {
        $this->signInAs('client');
        $this->postJson(route('cruises.create'), [])->assertStatus(403);
    }

    public function test_cruise_cannot_be_created_by_agent()
    {
        $this->signInAs('agent');
        $this->postJson(route('cruises.create'), [])->assertStatus(403);
    }
}
