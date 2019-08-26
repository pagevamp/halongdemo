<?php

namespace App\Services\Filters;

use App\Services\Response\ApiResponse;
use Illuminate\Http\Request;

abstract class Filters
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [];

    protected $model;

    protected $relationProxies = [];

    protected $response;

    /**
     * Create a new ThreadFilters instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new ApiResponse();
    }

    /**
     * Apply the filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        if (method_exists($this, 'applyFiltersBasedOnRoles')) {
            $this->applyFiltersBasedOnRoles();
        }

        foreach ($this->getFilters() as $filter => $value) {
            $methodName = camel_case($filter);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @return array
     */
    public function getFilters()
    {
        return array_filter($this->request->only($this->filters));
    }

    protected function filter($filters)
    {
        try {
            foreach ($filters as $databaseColumnName => $value) {
                $methodName = camel_case($databaseColumnName);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                    continue;
                }
                if (is_numeric($value)) {
                    $this->builder->where($databaseColumnName, '=', "$value");
                } else {
                    $this->builder->where($databaseColumnName, 'like', "%{$value}%");
                }
            }

            return $this->builder;
        } catch (\Exception $exception) {
            return $this->response->respondInvalidQuery($exception);
        }
    }

    protected function sortBy($field)
    {
        $methodName = 'sortBy'. ucfirst(camel_case($field));
        if (method_exists($this, $methodName)) {
            $this->$methodName();
        } else {
            $this->builder->getQuery()->orders = [];
            $this->builder->orderBy($field, $this->request->get('sort_order', 'asc'));
        }

        return $this->builder;
    }

    protected function includes($relations)
    {
        $relations = array_map(function ($relation) {
            return $this->relationProxies[$relation] ?? $relation;
        }, explode(',', $relations));

        return $this->builder->with($relations);
    }

    protected function withCount($relations)
    {
        $relations = array_map(function ($relation) {
            return $this->relationProxies[$relation] ?? $relation;
        }, explode(',', $relations));

        return $this->builder->withCount($relations);
    }
}
