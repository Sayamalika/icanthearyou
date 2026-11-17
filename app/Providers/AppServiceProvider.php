<?php

namespace App\Providers;

use App\Auth\DatabaseUserProvider;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('database-user', function ($app, array $config): DatabaseUserProvider {
            return new DatabaseUserProvider(
                $app['db']->connection($config['connection'] ?? null),
                $app['hash']
            );
        });
    }
}
