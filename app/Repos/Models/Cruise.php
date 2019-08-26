<?php

namespace App\Repos\Models;

use App\Repos\Traits\CanBeFeatured;
use App\Repos\Traits\DefaultImageSize;
use App\Repos\Traits\Filterable;
use App\Repos\Traits\HasMedia;
use App\Repos\Traits\HasSlug;
use App\Services\Media\HasMediaInterface;
use App\Services\Schema\HasSchema;
use DB;

class Cruise extends AbstractModel
{
    use Filterable, HasSlug, HasMedia, CanBeFeatured, DefaultImageSize;

    const UNPUBLISHED = 0;
    const PUBLISHED = 1;

    protected $fillable = [
        'name',
        'price',
        'published',
        'featured_index',
        'short_description',
        'long_description',
        'video',
        'slug',
        'route_id',
        'star',
        'total_average_rating',
    ];

    private $ratingKeys = [
        'cleanliness_rating',
        'comfort_rating',
        'staff_rating',
        'food_rating',
        'service_rating',
        'value_for_money_rating',
    ];

    public function getMeta($name, $defaultValue = null)
    {
        if (!$this->relationLoaded('metas')) {
            return $defaultValue;
        }
        return $this->metas->where('name', $name)->first()->value ?? $defaultValue;
    }

    public function metas()
    {
        return $this->hasMany(CruiseMeta::class, 'cruise_id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'cruise_activities', 'cruise_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'cruise_facilities', 'cruise_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'cruise_id');
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class, 'cruise_id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'cruise_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'cruise_categories', 'cruise_id', 'category_id')->withTimestamps();
    }

    public function categoryIds()
    {
        return $this->categories()->select(['categories.id']);
    }

    public function route()
    {
        return $this->belongsTo(CruiseRoute::class, 'route_id', 'id');
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Itinerary::class);
    }

    public function averageReviews()
    {
        return $this->reviews()->where('published', 1)->groupBy('itineraries.cruise_id')->select([
            DB::raw('ROUND(AVG(cleanliness_rating), 1) as cleanliness_rating'),
            DB::raw('ROUND(AVG(comfort_rating), 1) as comfort_rating'),
            DB::raw('ROUND(AVG(staff_rating), 1) as staff_rating'),
            DB::raw('ROUND(AVG(food_rating), 1) as food_rating'),
            DB::raw('ROUND(AVG(service_rating), 1) as service_rating'),
            DB::raw('ROUND(AVG(value_for_money_rating), 1) as value_for_money_rating'),
            DB::raw('count(*) as total_reviews'),
        ]);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    public function scopeNotPublished($query)
    {
        return $query->where('published', 0);
    }

    public function scopeByRouteSlugs($query, $slugs)
    {
        return $query->whereHas('route', function ($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
        });
    }

    public function scopeByMinPrice($query, $price)
    {
        return $query->where('price', '>=', $price);
    }

    public function scopeByMaxPrice($query, $price)
    {
        return $query->where('price', '<=', $price);
    }

    public function scopeByCategorySlugs($query, $slugs)
    {
        return $query->whereHas('categories', function ($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
        });
    }

    public function scopeByActivitySlugs($query, $slugs)
    {
        return $query->whereHas('activities', function ($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
        });
    }

    public function scopeByFacilitySlugs($query, $slugs)
    {
        return $query->whereHas('facilities', function ($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
        });
    }

    public function scopeByTotalCabinRange($query, $min, $max)
    {
        return $query->whereHas('metas', function ($query) use ($min, $max) {
            $query->where('name', 'cabins')
                ->whereBetween('value', [$min, $max]);
        });
    }

    public function scopeByOfferSlugs($query, $slugs)
    {
        return $query->whereHas('promotions', function ($query) use ($slugs) {
            $query->whereHas('category', function ($query) use ($slugs) {
                $query->whereIn('slug', $slugs)->where('published', 1);
            })->where('active', 1);
        });
    }

    public function scopeByRatings($query, $ratings)
    {
        return $query->whereIn('star', $ratings);
    }

    public function scopeByMinAverageGuestRating($query, $rating)
    {
        return $query->where('total_average_rating', '>=', $rating);
    }

    public function scopeByMaxAverageGuestRating($query, $rating)
    {
        return $query->where('total_average_rating', '<=', $rating)->where('total_average_rating', '!=', 0);
    }

    public function scopeBySlugOrId($query, $id)
    {
        return $query->where('slug', $id)->orWhere('id', $id);
    }

    public function getSingleImageCategories(): array
    {
        return ['og_image'];
    }

    public function deleteMediaOnCascade()
    {
        return $this->experiences()->delete();
    }

    public function getCategoryIds()
    {
        return $this->categoryIds->pluck('id');
    }

    public function updateAverageRating(Review $review)
    {
        $allowedRatings = $review->only($this->ratingKeys);
        $totalRatingsSum = array_sum($allowedRatings);
        $averageRating = $totalRatingsSum / \count($allowedRatings);
        $this->update(['total_average_rating' => $averageRating]);

        return true;
    }

    public function scopeByHighPrice($query)
    {
        return $query->orderBy('price', 'desc');
    }

    public function scopeByTopReviewed($query)
    {
        return $query->orderBy('total_average_rating', 'desc');
    }

    public function scopeByCruiseStar($query)
    {
        return $query->orderBy('star', 'desc');
    }

    public function scopeByTopPicks($query)
    {
        return $query->orderBy('featured_index', 'DESC');
    }

    public function scopeByLowPrice($query)
    {
        return $query->orderBy('price', 'asc');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getShortDescription()
    {
        return $this->short_description;
    }

    public function getFeaturedImage()
    {
        return $this->getMeta('og_image');
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getTotalAverageRatings()
    {
        return $this->total_average_rating;
    }

    public function getRatingKeys()
    {
        return $this->ratingKeys;
    }
}
