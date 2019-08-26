<?php

namespace Tests;

use App\Exceptions\Handler;
use App\Repos\Models\Cruise;
use App\Repos\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected const UPDATED_STATUS = 201;
    protected const CREATED_STATUS = 201;
    protected const VALIDATION_FAILED_STATUS = 422;
    protected const HTTP_FOUND_STATUS = 200;
    protected const DELETED_STATUS = 200;
    protected const INTERNAL_ERROR_STATUS = 500;
    protected const FORBIDDEN_ERROR_STATUS = 403;
    protected const UNAUTHORIZED_ERROR_STATUS = 401;

    protected $oldExceptionHandler;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getRoles()
    {
        return array_values(User::$roles);
    }

    public function createCruise()
    {
        return factory(Cruise::class)->states(
            'published',
            'with_route',
            'create_default_metas',
            'create_default_categories',
            'create_default_facilities',
            'create_default_itineraries',
            'create_default_experiences',
            'create_default_activities'
        )->create();
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new class() extends Handler {
            public function __construct()
            {
            }

            public function report(\Exception $e)
            {
            }

            public function render($request, \Exception $e)
            {
                throw $e;
            }
        });
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }

    protected function signIn($user = null)
    {
        $user = $user ?: create(User::class);

        $this->actingAs($user);

        return $user;
    }

    protected function getResourceCollection($resource)
    {
        $data = $resource->resource;
        $data = $resource->toArray($data);
        return new Collection($data);
    }

    protected function signInAs($roleSlug = 'client', $user = null)
    {
        if (null === $user) {
            $user = createUserWithRole($roleSlug);
        }

        return $this->signIn($user);
    }
}
