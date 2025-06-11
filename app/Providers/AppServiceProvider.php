<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    // Gunakan Bootstrap pagination secara default
    Paginator::useBootstrap();
    
    // Gunakan tampilan pagination kustom
    Paginator::defaultView('vendor.pagination.custom-pagination');
    Paginator::defaultSimpleView('vendor.pagination.simple-custom-pagination');
}
}
