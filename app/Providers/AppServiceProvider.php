<?php

namespace App\Providers;

use App\Observers\CruiseObserver;
use App\Repos\Models\Cruise;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'cruises' => Cruise::class,
        ]);
        Cruise::observe(CruiseObserver::class);
    }
}
