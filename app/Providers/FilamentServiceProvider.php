<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\RoleResource;
use Filament\Facades\Filament;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerResources([
                RoleResource::class, // daftarkan versi milikmu
                PermissionResource::class
            ]);
        });
    }
}
