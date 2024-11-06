<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Paper;
use App\Observers\PaperObserver;
use App\Services\PaperFileUploadService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaperFileUploadService::class, function ($app) {
            return new PaperFileUploadService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // parent::boot();
        Paper::observe(PaperObserver::class);
    }
}
