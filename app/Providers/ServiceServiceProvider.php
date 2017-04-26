<?php

namespace App\Providers;

use App\PerformanceRule;
use App\Repositories\PerformanceRule\PerformanceRuleInterface;
use App\Repositories\Report\ReportInterface;
use App\Repositories\User\UserInterface;
use App\Services\PerformanceRuleService;
use App\Services\ReportService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
