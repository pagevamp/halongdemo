<?php

namespace App\Http\Resources;

use App\Services\Schema\CruiseSchema;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CruiseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $this->whenLoaded('categories', function () {
            $this->loadMissing('categoryIds');
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => parseNumber($this->price),
            'published' => $this->published,
            'featured_index' => $this->featured_index,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'video' => $this->video,
            'star' => $this->star,
            'built_year' => $this->getMeta('built_year'),
            'floors' => $this->getMeta('floors'),
            'cabins' => $this->getMeta('cabins'),
            'guest_capacity' => $this->getMeta('guest_capacity'),
            'height' => $this->getMeta('height'),
            'length' => $this->getMeta('length'),
            'width' => $this->getMeta('width'),
            'average_reviews' => [
                'cleanliness_rating' => parseNumber($this->getMeta('cleanliness_rating')),
                'comfort_rating' => parseNumber($this->getMeta('comfort_rating') ?? 0),
                'staff_rating' => parseNumber($this->getMeta('staff_rating') ?? 0),
                'food_rating' => parseNumber($this->getMeta('food_rating') ?? 0),
                'service_rating' => parseNumber($this->getMeta('service_rating') ?? 0),
                'value_for_money_rating' => parseNumber($this->getMeta('value_for_money_rating') ?? 0),
                'total_reviews' => parseNumber($this->getMeta('total_reviews')),
                'total_average_rating' => parseNumber($this->total_average_rating),
            ],
            'boarding_time' => $this->when($this->getMeta('boarding_time'), function () {
                return (new Carbon($this->getMeta('boarding_time')))->format('H:i');
            }),
            'disembarking_time' => $this->when($this->getMeta('disembarking_time'), function () {
                return (new Carbon($this->getMeta('disembarking_time')))->format('H:i');
            }),
            'seo_title' => $this->getMeta('seo_title'),
            'seo_description' => $this->getMeta('seo_description'),
            'seo_keywords' => $this->getMeta('seo_keywords'),
            'og_image' => $this->getMeta('og_image'),
            'booking_policy' => $this->getMeta('booking_policy'),
            'why_book_this_cruise' => $this->getMeta('why_book_this_cruise'),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'category_ids' => $this->whenLoaded('categoryIds', function () {
                return $this->getCategoryIds();
            }),
            'itineraries' => ItineraryResource::collection($this->whenLoaded('itineraries')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'experiences' => ExperienceResource::collection($this->whenLoaded('experiences')),
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'route' => new RouteResource($this->whenLoaded('route')),
            'media' => $this->when($this->whenLoaded('media') instanceof Collection, function () {
                return $this->getCategorisedMedia();
            }),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'promotions' => PromotionResource::collection($this->whenLoaded('promotions')),
            'schema' => $this->when($request->has('schema'), function () {
                return (new CruiseSchema($this->resource))->get();
            }),
        ];
    }
}
