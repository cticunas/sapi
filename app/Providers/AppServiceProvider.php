<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('App\Repository\PlanRepositoryI','App\Repository\PlanRepository');
        $this->app->bind('App\Repository\JournalRepositoryI','App\Repository\JournalRepository');
        $this->app->bind('App\Repository\PersonRepositoryI','App\Repository\PersonRepository');
        $this->app->bind('App\Repository\RenacytRepositoryI','App\Repository\RenacytRepository');
        $this->app->bind('App\Repository\UserRepositoryI','App\Repository\UserRepository');
        $this->app->bind('App\Repository\CategoryRepositoryI','App\Repository\CategoryRepository');
        $this->app->bind('App\Repository\OrganizationRepositoryI','App\Repository\OrganizationRepository');
        $this->app->bind('App\Repository\ResearchRepositoryI','App\Repository\ResearchRepository');
        $this->app->bind('App\Repository\AuthorRepositoryI','App\Repository\AuthorRepository');
        $this->app->bind('App\Repository\FileRepositoryI','App\Repository\FileRepository');
        $this->app->bind('App\Repository\OutcomeRepositoryI','App\Repository\OutcomeRepository');
        $this->app->bind('App\Repository\MasterRepositoryI','App\Repository\MasterRepository');
        $this->app->bind('App\Repository\DocumentRepositoryI','App\Repository\DocumentRepository');
        $this->app->bind('App\Repository\EventRepositoryI','App\Repository\EventRepository');
    }

    public function boot()
    {
        \Carbon\Carbon::setLocale("app.locale");
        Schema::defaultStringLength(191);
    }
}
