<?php

namespace App\Providers;


use App\Bonus;
use App\Repositories\Bonus\BonusRepository;

use App\Report;
use App\Repositories\Report\ReportRepository;

use App\User;
use App\Repositories\User\UserRepository;

use App\PerformanceRule;
use App\Repositories\PerformanceRule\PerformanceRuleRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Bind Report Repository
         */
        $this->app->bind('App\Repositories\Report\ReportInterface', function(){
            return new ReportRepository(new Report());
        });

        /*
         * Bind User Repository
         */
        $this->app->bind('App\Repositories\User\UserInterface', function(){
            return new UserRepository(new User());
        });

        /*
         * Bind PerformanceRule Repository
         */
        $this->app->bind('App\Repositories\PerformanceRule\PerformanceRuleInterface', function(){
            return new PerformanceRuleRepository(new PerformanceRule());
        });

        /*
         * Bind Bonus Repository
         */
        $this->app->bind('App\Repositories\Bonus\BonusInterface', function(){
            return new BonusRepository(new Bonus());
        });
    }
}
