<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Resources\ClassPlanResource;
use App\Filament\Resources\GradeResource;
use App\Filament\Resources\ScheduleResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Resources\StudentResource;
use App\Filament\Widgets\MyCalendarWidget;
use App\Models\ClassPlan;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class ParentsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('parents')
            ->path('/parents')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('Essencial')
            ->favicon('images/logo.png')
            ->colors([
                'primary' => '#BC3C3F',
            ])
            ->discoverResources(in: app_path('Filament/Parents/Resources'), for: 'App\\Filament\\Parents\\Resources')
            ->discoverPages(in: app_path('Filament/Parents/Pages'), for: 'App\\Filament\\Parents\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                StudentResource::class,
                ClassPlanResource::class,
                GradeResource::class,
                ScheduleResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Parents/Widgets'), for: 'App\\Filament\\Parents\\Widgets')
            ->widgets([
                MyCalendarWidget::class,

            ])
            ->plugins([
               
            FilamentBackgroundsPlugin::make()
                ->imageProvider(
                        MyImages::make()
                            ->directory('images/myImages')
                    ),
                FilamentSpatieRolesPermissionsPlugin::make()
                 
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
            ]);
    }
}
