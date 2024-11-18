<?php

namespace App\Providers;

use App\Services\JudgeService;
use Illuminate\Support\ServiceProvider;

class JudgeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Mendaftarkan JudgeService sebagai singleton
        $this->app->singleton(JudgeService::class, function ($app) {
            return new JudgeService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
