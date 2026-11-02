<?php

namespace App\Providers;

use Illuminate\Foundation\DevCommands;
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
        // Run the scheduler (daily reminders) alongside `php artisan dev`.
        if ($this->app->runningInConsole()) {
            DevCommands::artisan('schedule:work', 'schedule');
        }
    }
}
