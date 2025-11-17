<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Site;
use App\Observers\PageObserver;
use App\Observers\PageBlockObserver;
use App\Observers\SiteObserver;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // Register model observers for automatic cache invalidation
        Site::observe(SiteObserver::class);
        Page::observe(PageObserver::class);
        PageBlock::observe(PageBlockObserver::class);
    }
}
