<?php

namespace Tests\Feature\Cruise;

use App\Repos\Models\Cruise;
use App\Repos\Models\CruiseActivity;
use App\Repos\Models\CruiseCategory;
use App\Repos\Models\CruiseFacility;
use App\Repos\Models\CruiseMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CruiseUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_cruise_can_be_updated_by_admin_only()
    {
        $this->signInAs('admin');

        $cruise = factory(Cruise::class)->states(
            'published',
            'with_route',
            'create_default_metas',
            'create_default_categories',
            'create_default_facilities',
            'create_default_activities'
        )->create();

        $params = factory(Cruise::class)->states(
            'unpublished',
            'with_route',
            'with_post_metas',
            'with_categories',
            'with_activities',
            'with_facilities'
        )->make();
        //PLEASE UPDATE ALL ROUTE NAMES TO PLURAL Eg: cruises.update
        $this->patchJson(route('cruises.update', $cruise->id), $params->toArray())->assertStatus(201);
        $cruise->refresh();
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
                'cruise_id' => $cruise->id,
            ]);
        }

        foreach ($params['category_ids'] as $categoryId) {
            $this->assertDatabaseHas('cruise_categories', [
                'category_id' => $categoryId,
                'cruise_id' => $cruise->id,
            ]);
        }
        $this->assertCount(\count($params['category_ids']), CruiseCategory::all());

        foreach ($params['activity_ids'] as $activityId) {
            $this->assertDatabaseHas('cruise_activities', [
                'activity_id' => $activityId,
                'cruise_id' => $cruise->id,
            ]);
        }
        $this->assertCount(\count($params['activity_ids']), CruiseActivity::all());

        foreach ($params['facility_ids'] as $facilityId) {
            $this->assertDatabaseHas('cruise_facilities', [
                'facility_id' => $facilityId,
                'cruise_id' => $cruise->id,
            ]);
        }
        $this->assertCount(\count($params['facility_ids']), CruiseFacility::all());
    }

    public function test_cruise_cannot_be_updated_by_client()
    {
        $this->signInAs('client');
        $cruise = factory(Cruise::class)->create();
        $this->patchJson(route('cruises.update', $cruise->id), [])->assertStatus(403);
    }

    public function test_cruise_cannot_be_updated_by_agent()
    {
        $this->signInAs('agent');
        $cruise = factory(Cruise::class)->create();
        $this->patchJson(route('cruises.update', $cruise->id), [])->assertStatus(403);
    }
}
