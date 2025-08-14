<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;
use App\Filament\Pages\Auth\Login;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Romalramos\FilamentLogger\FilamentLoggerPlugin;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use App\Models\Company;
// use App\Scopes\ApplyTenantScopes;
use App\Http\Middleware\ApplyTenantScopes;
// use App\Http\Middleware\ApplyTenantScopes;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        $isLogin = request()->routeIs('filament.admin.auth.login');
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->tenant(Company::class)
            ->login()
            ->userMenuItems([
                UserMenuItem::make()
                    ->label(fn() => 'Nama: ' . auth()->user()?->name ?? '-'),
                UserMenuItem::make()
                    ->label(fn() => 'Email: ' . auth()->user()?->email ?? '-'),
                UserMenuItem::make()
                    ->label(fn() => 'Role: ' . auth()->user()?->roles->pluck('name')->join(', ') ?? '-'),
            ])
            ->profile()
            ->passwordReset()
            // ->brandLogo(asset('siap_login.svg'))
            ->brandLogo(function () {
                if (request()->routeIs('filament.admin.auth.login')) {
                    return asset('siap_login.svg'); // Logo login
                }

                return asset('Logo Siap.svg'); // Logo dashboard
            })
            // ->brandLogoHeight('3rem');
            ->brandLogoHeight('4rem')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('20rem')

            // ->emailVerification()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                'Jabatan dan Hak Akses',
                'Manajemen Data Master',
                'Customer',
                'Keuangan',
                'Jasa Pemetaan',
                'Jasa Sewa',
            ])
            ->tenantMiddleware([
                ApplyTenantScopes::class,
            ], isPersistent: true)
            ->plugins([
                ActivitylogPlugin::make(),
            ]);
    }
}
