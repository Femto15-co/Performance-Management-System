<?php

namespace App\Providers;


use App\Bonus;
use App\Repositories\Bonus\BonusRepository;

use App\Report;
use App\Repositories\Report\ReportRepository;

use App\Comment;
use App\Repositories\Comment\CommentRepository;

use App\Role;
use App\Repositories\Role\RoleRepository;

use App\User;
use App\Repositories\User\UserRepository;

use App\PerformanceRule;
use App\Repositories\PerformanceRule\PerformanceRuleRepository;


use App\Defect;
use App\Repositories\Defect\DefectRepository;

use App\EmployeeType;
use App\Repositories\EmployeeType\EmployeeTypeRepository;


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
         * Bind Comment Repository
         */
        $this->app->bind('App\Repositories\Comment\CommentInterface', function(){
            return new CommentRepository(new Comment());
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

        /*
        * Bind Defect Repository
        */
        $this->app->bind('App\Repositories\Defect\DefectInterface', function(){
            return new DefectRepository(new Defect());
        });
        /*
        * Bind EmployeeType Repository
        */
        $this->app->bind('App\Repositories\EmployeeType\EmployeeTypeInterface', function(){
            return new EmployeeTypeRepository(new EmployeeType());
        });

        /*
        * Bind Role Repository
        */
        $this->app->bind('App\Repositories\Role\RoleInterface', function(){
            return new RoleRepository(new Role());

        });
    }
}
