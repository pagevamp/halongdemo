<?php

namespace App\Providers;

use App\Events\Registrar\BookingRegistrar;
use App\Events\Registrar\CruiseRegistrar;
use App\Events\Registrar\ItineraryRegistrar;
use App\Events\Registrar\MediaRegistrar;
use App\Events\Registrar\UserRegistrar;
use App\Services\Media\HasMediaInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
        $this->registerEvents();
    }

    private function registerEvents()
    {
        (new CruiseRegistrar())->register();
        $this->registerGlobalEloquentDeletedEvent();
    }

    private function registerGlobalEloquentDeletedEvent()
    {
        Event::listen(['eloquent.deleted: *'], function ($namespace, $models) {
            foreach ($models as $model) {
                if ($model instanceof HasMediaInterface) {
                    $model->media()->each(function ($item) {
                        $item->delete();
                    });
                }
            }
        });
    }
}
