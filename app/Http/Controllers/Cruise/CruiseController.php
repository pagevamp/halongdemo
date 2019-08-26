<?php

namespace App\Http\Controllers\Cruise;

use App\Events\Cruise\Created;
use App\Events\Cruise\Updated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cruise\CruiseRequest;
use App\Http\Resources\CruiseResource;
use App\Repos\Models\Cruise;
use App\Services\Filters\CruiseFilters;

class CruiseController extends Controller
{
    protected $cruise;

    public function __construct(Cruise $cruise)
    {
        parent::__construct();
        $this->cruise = $cruise;
    }

    public function index(CruiseFilters $filters)
    {
        $cruises = $this->cruise->filter($filters)->paginate(getPerPage());

        return $this->response->setPagination($cruises)
            ->respond(CruiseResource::collection($cruises));
    }

    public function show($id, CruiseFilters $filters)
    {
        $cruise = $this->cruise->findBySlugOrFail($id)->filter($filters)->firstOrFail();

        return $this->response
            ->respond(new CruiseResource($cruise));
    }

    public function store(CruiseRequest $request)
    {
        $cruise = $this->cruise->create($request->validated());
        event(new Created($cruise, $request->validated()));

        return $this->response->setMessage('Cruise created successfully!')
            ->respondCreated(new CruiseResource($cruise));
    }

    public function update($id, CruiseRequest $request)
    {
        $cruise = $this->cruise->findBySlugOrFail($id)->firstOrFail();
        $cruise->update($request->validated());
        event(new Updated($cruise, $request->validated()));

        return $this->response
            ->respondUpdated(new CruiseResource($cruise));
    }

    public function delete($id, Cruise $cruise)
    {
        $cruise = $cruise->findBySlugOrFail($id)->firstOrFail();

        $this->authorize('delete', Cruise::class);

        $cruise->delete();

        return $this->response->setMessage('Cruise deleted successfully!')
            ->respondDeleted();
    }
}
