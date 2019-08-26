<?php

namespace App\Services\Filters;

class CruiseFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['filter', 'sort_by', 'includes', 'schema'];

    public function featured(bool $flag)
    {
        if ($flag) {
            return $this->builder->featured();
        }

        return $this->builder->notFeatured();
    }

    public function category($category)
    {
        return $this->builder->whereHas('categories', function ($q) use ($category) {
            $q->where('slug', $category);
        });
    }

    public function publishedReviews($limit = null)
    {
        $limit = $limit === 'all' || $limit === 'null' || $limit === 'undefined' ? null : $limit;
        return $this->builder->with(['reviews' => function ($query) use ($limit) {
            return $limit > 0 ? $query->published()->limit($limit) : $query->published();
        }]);
    }

    public function activePromotions($limit = null)
    {
        $limit = $limit === 'all' || $limit === 'null' || $limit === 'undefined' ? null : $limit;
        return $this->builder->with(['promotions' => function ($query) use ($limit) {
            $query = $query->with('media');
            return $limit > 0 ? $query->active()->limit($limit) : $query->active();
        }]);
    }

    public function activeFeaturedPromotions($limit = null)
    {
        $limit = $limit === 'all' || $limit === 'null' || $limit === 'undefined' ? null : $limit;
        return $this->builder->with(['promotions' => function ($query) use ($limit) {
            $query = $query->with('media')->active()->featured()->orderBy('featured_index', 'desc');
            return $limit > 0 ? $query->limit($limit) : $query;
        }]);
    }

    public function routes($slugs)
    {
        return $this->builder->byRouteSlugs(explode(',', $slugs));
    }

    public function categories($slugs)
    {
        return $this->builder->byCategorySlugs(explode(',', $slugs));
    }

    public function facilities($slugs)
    {
        return $this->builder->byFacilitySlugs(explode(',', $slugs));
    }

    public function activities($slugs)
    {
        return $this->builder->byActivitySlugs(explode(',', $slugs));
    }

    public function ratings($ratings)
    {
        return $this->builder->byRatings(explode(',', $ratings));
    }

    public function minGuestRating($rating)
    {
        return $this->builder->byMinAverageGuestRating($rating);
    }

    public function maxGuestRating($rating)
    {
        return $this->builder->byMaxAverageGuestRating($rating)->toSql();
    }

    public function minPrice($price)
    {
        return $this->builder->byMinPrice($price);
    }

    public function deals($slugs)
    {
        return $this->builder->byOfferSlugs(explode(',', $slugs));
    }

    public function maxPrice($price)
    {
        return $this->builder->byMaxPrice($price);
    }

    public function highPrice()
    {
        return $this->builder->byHighPrice();
    }

    public function lowPrice()
    {
        return $this->builder->byLowPrice();
    }

    public function cabins($numbers)
    {
        $numbers = str_replace('_', ',', $numbers);
        $totalCabins = explode(',', $numbers);
        sort($totalCabins);
        $minNumber = (int) $totalCabins[0];
        $maxNumber = (int) end($totalCabins);

        return $this->builder->byTotalCabinRange($minNumber, $maxNumber);
    }

    public function orderBy($field)
    {
        if ('high_price' === $field) {
            return $this->builder->byHighPrice();
        }

        if ('low_price' === $field) {
            return $this->builder->byLowPrice();
        }

        if ('stars' === $field) {
            return $this->builder->byCruiseStar();
        }

        if ('top_ratings' === $field) {
            return $this->builder->byTopReviewed();
        }

        if ('top_picks' === $field) {
            return $this->builder->byTopPicks();
        }

        return $this->builder;
    }
}
