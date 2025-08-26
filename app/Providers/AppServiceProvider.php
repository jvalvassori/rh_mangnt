<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        // ---------------------------
        //GATES
        // ----------------------------

        // Define a gate checks if the user is admin
        Gate::define('admin', function(){
            return auth()->user()->role === 'admin';
        });

        // Define a gate checks if the user is rh
        Gate::define('rh', function(){
            return auth()->user()->role === 'rh';
        });

        // Define a gate checks if the user is colaborator
        Gate::define('colaborator', function(){
            return auth()->user()->role === 'colaborator';
        });
    }
    
}
