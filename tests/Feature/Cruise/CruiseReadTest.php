<?php

namespace Tests\Feature\Cruise;

use App\Http\Resources\CruiseResource;
use App\Repos\Models\Activity;
use App\Repos\Models\Category;
use App\Repos\Models\Cruise;
use App\Repos\Models\CruiseActivity;
use App\Repos\Models\CruiseCategory;
use App\Repos\Models\CruiseFacility;
use App\Repos\Models\CruiseRoute;
use App\Repos\Models\Facility;
use App\Services\Response\ApiResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CruiseReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_all_cruise_data_are_loaded()
    {
        create(Cruise::class, [], 3);

        $filters = [
            'includes' => 'metas,activities,categories,facilities,itineraries,experiences,route',
        ];

        $this->getJson(route('cruises.index', $filters))->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'slug',
                        'short_description',
                        'long_description',
                        'published',
                        'video',
                        'price',
                        'route',
                        'featured_index',
                        'activities',
                        'categories',
                        'facilities',
                        'itineraries',
                        'experiences',
                    ],
                ],
                'pagination',
            ]);
    }

    public function test_single_cruise_data_are_loaded()
    {
        $cruise = $this->createCruise();

        $filters = [
            'slug' => $cruise->id,
            'includes' => 'metas,activities,categories,facilities,itineraries,experiences,route',
            'schema' => 1,
        ];

        $this->getJson(route('cruises.show', $filters))->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'short_description',
                    'long_description',
                    'published',
                    'video',
                    'price',
                    'route',
                    'featured_index',
                    'categories',
                    'facilities',
                    'itineraries',
                    'experiences',
                    'schema',
                ],
            ]);
    }

    public function test_if_category_filters_work_correctly()
    {
        $this->createMultipleCruise(5);
        $randomCategory = Category::all()->random(2);
        $filters = [
            'filter' => [
                'categories' => implode(',', $randomCategory->pluck('slug')->toArray()),
            ],
        ];

        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);

        $totalCruise = CruiseCategory::whereIn('category_id', $randomCategory->pluck('id')->toArray())->groupBy('cruise_id')->get()->count();

        $responseData = json_decode($response->getContent(), 1);

        $this->assertSame((int) $totalCruise, \count($responseData['data']));
    }

    public function test_if_activity_filters_work_correctly()
    {
        $this->createMultipleCruise(5);
        $randomCategory = Activity::all()->random(2);
        $filters = [
            'filter' => [
                'activities' => implode(',', $randomCategory->pluck('slug')->toArray()),
            ],
        ];

        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);

        $totalCruise = CruiseActivity::whereIn('activity_id', $randomCategory->pluck('id')->toArray())->groupBy('cruise_id')->get()->count();

        $responseData = json_decode($response->getContent(), 1);

        $this->assertSame((int) $totalCruise, \count($responseData['data']));
    }

    public function test_if_facility_filters_work_correctly()
    {
        $this->createMultipleCruise(5);
        $randomCategory = Facility::all()->random(2);
        $filters = [
            'filter' => [
                'facilities' => implode(',', $randomCategory->pluck('slug')->toArray()),
            ],
        ];

        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);
        $totalCruise = CruiseFacility::whereIn('facility_id', $randomCategory->pluck('id')->toArray())->groupBy('cruise_id')->get()->count();
        $responseData = json_decode($response->getContent(), 1);
        $this->assertSame((int) $totalCruise, \count($responseData['data']));
    }

    public function test_if_route_filters_work_correctly()
    {
        $this->createMultipleCruise(5);
        $randomCategory = CruiseRoute::all()->random(2);
        $filters = [
            'filter' => [
                'routes' => implode(',', $randomCategory->pluck('slug')->toArray()),
            ],
        ];

        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);
        $totalCruise = Cruise::whereIn('route_id', $randomCategory->pluck('id')->toArray())->groupBy('id')->get()->count();
        $responseData = json_decode($response->getContent(), 1);
        $this->assertSame((int) $totalCruise, \count($responseData['data']));
    }

    public function test_if_price_filter_works_correctly()
    {
        $this->createMultipleCruise(5);
        $filters = [
            'filter' => [
                'max_price' => 50,
                'min_price' => 1,
            ],
        ];

        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);

        $totalCruise = Cruise::whereBetween('price', [1, 50])->get()->count();

        $responseData = json_decode($response->getContent(), 1);

        $this->assertSame((int) $totalCruise, \count($responseData['data']));
    }

    public function test_if_high_price_order_filter_works_correctly()
    {
        $apiResponse = new ApiResponse();
        $this->createMultipleCruise(5);
        $filters = [
            'filter' => [
                'order_by' => 'high_price',
            ],
        ];
        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);
        $allCruise = Cruise::orderBy('price', 'desc')->paginate(getPerPage());
        $responseData = $response->getContent();
        $expectedResponse = $apiResponse->setPagination($allCruise)->respond(CruiseResource::collection($allCruise))->getContent();

        $this->assertSame($responseData, $expectedResponse);
    }

    public function test_if_low_price_order_filter_works_correctly()
    {
        $apiResponse = new ApiResponse();
        $this->createMultipleCruise(5);
        $filters = [
            'filter' => [
                'order_by' => 'low_price',
            ],
        ];
        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);
        $allCruise = Cruise::orderBy('price', 'asc')->paginate(getPerPage());
        $responseData = $response->getContent();
        $expectedResponse = $apiResponse->setPagination($allCruise)->respond(CruiseResource::collection($allCruise))->getContent();

        $this->assertSame($responseData, $expectedResponse);
    }

    public function test_if_star_rating_order_filter_works_correctly()
    {
        $apiResponse = new ApiResponse();
        $this->createMultipleCruise(5);
        $filters = [
            'filter' => [
                'order_by' => 'stars',
            ],
        ];
        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);
        $allCruise = Cruise::orderBy('star', 'desc')->paginate(getPerPage());

        $responseData = $response->getContent();
        $expectedResponse = $apiResponse->setPagination($allCruise)->respond(CruiseResource::collection($allCruise))->getContent();

        $this->assertSame($responseData, $expectedResponse);
    }

    public function test_if_top_picks_order_filter_works_correctly()
    {
        $apiResponse = new ApiResponse();
        $this->createMultipleCruise(5);
        $filters = [
            'filter' => [
                'order_by' => 'top_picks',
            ],
        ];
        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);

        $allCruise = Cruise::orderBy('featured_index', 'desc')->paginate(getPerPage());

        $responseData = $response->getContent();
        $expectedResponse = $apiResponse->setPagination($allCruise)->respond(CruiseResource::collection($allCruise))->getContent();

        $this->assertSame($responseData, $expectedResponse);
    }

    public function test_if_cabins_order_filter_works_correctly()
    {
        $apiResponse = new ApiResponse();
        $this->createMultipleCruise(5);
        $filters = [
            'filter' => [
                'cabins' => '1_2,2,3',
            ],
        ];
        $response = $this->getJson(route('cruises.index', $filters))->assertStatus(200);

        $allCruise = Cruise::whereHas('metas', function ($query) {
            $query->where('name', 'cabins')
                ->whereBetween('value', [1, 3]);
        })->paginate(getPerPage());

        $responseData = $response->getContent();

        $expectedResponse = $apiResponse->setPagination($allCruise)->respond(CruiseResource::collection($allCruise))->getContent();

        $this->assertSame($responseData, $expectedResponse);
    }

    private function createMultipleCruise($threshold)
    {
        for ($i = 0; $i < $threshold; ++$i) {
            $this->createCruise();
        }
    }
}
