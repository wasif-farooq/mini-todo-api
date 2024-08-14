<?php

namespace App\Providers;

use App\Policies\TaskPolicy;
use App\Services\AuthService;
use App\Services\TaskService;
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
        // Register AuthService here
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService;
        });
        $this->app->singleton(TaskService::class, function ($app) {
            return new TaskService;
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Gate::define('update-task', [TaskPolicy::class, 'update']);
        Gate::define('delete-task', [TaskPolicy::class, 'delete']);
        Gate::define('change-status-task', [TaskPolicy::class, 'changestatus']);
    }
}
