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
        /*
         * Bind Report Service
         */
        $this->app->bind(ReportService::class, function($app){
            return new ReportService(
                //Inject into ReportInterface
                $app->make(ReportInterface::class),
                $app->make(PerformanceRuleInterface::class)
            );
        });

        /*
         * Bind User Service
         */
        $this->app->bind(UserService::class, function($app){
            return new UserService(
            //Inject into UserInterface
                $app->make(UserInterface::class)
            );
        });

        /*
         * Bind PerformanceRule Service
         */
        $this->app->bind(PerformanceRuleService::class, function($app){
            return new PerformanceRuleService(
            //Inject into ReportInterface
                $app->make(PerformanceRuleInterface::class)
            );
        });
    }
}
