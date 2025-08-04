<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\StatusPembayaran;
use App\Observers\ProjectObserver;
use App\Observers\PembayaranObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\StatusPekerjaan;
use App\Observers\StatusPekerjaanObserver;


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
        StatusPembayaran::observe(PembayaranObserver::class);
        Project::observe(ProjectObserver::class);
        StatusPekerjaan::observe(StatusPekerjaanObserver::class);
    }
}
