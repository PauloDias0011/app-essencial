<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Widgets\DashboardWidgets as WidgetsDashboardWidgets;
use App\Filament\Widgets\MyCalendarWidget;
use App\Filament\Widgets\RecentClassPlansTable as WidgetsRecentClassPlansTable;
use App\Filament\Widgets\SchedulesCalendar;
use App\Filament\Widgets\UpcomingSchedulesCalendar as WidgetsUpcomingSchedulesCalendar;
use DashboardWidgets;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use GradeTrendChart;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use MonthlyExpensesChart;
use RecentClassPlansTable;
use StudentDistributionChart;
use UpcomingSchedulesCalendar;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('360s')
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WidgetsDashboardWidgets::class,
                StudentDistributionChart::class,
                GradeTrendChart::class,
                WidgetsRecentClassPlansTable::class,
                WidgetsUpcomingSchedulesCalendar::class,
                MonthlyExpensesChart::class,
                SchedulesCalendar::class,   
                MyCalendarWidget::class,
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
