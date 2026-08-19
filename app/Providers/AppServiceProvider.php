<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\VisitInfo;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('*', function ($view) {
            static $settings = null;
            static $visit = null;
            $settings ??= SiteSetting::current()->loadMissing('media');
            $visit ??= VisitInfo::current();
            $view->with('siteSettings', $settings);
            $view->with('visitInfo', $visit);
        });

        try {
            SiteSetting::current()->applyMailConfig();
        } catch (\Throwable) {
            // DB may be unavailable during early boot / artisan package discovery.
        }
    }
}
