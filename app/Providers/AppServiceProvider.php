<?php

namespace App\Providers;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
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
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Educação')
                    ->icon('heroicon-o-clipboard')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Gerenciamento')
                    ->icon('heroicon-s-cog'),
            ]);

          
        });

        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true: null;
        });

        Filament::serving(function () {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
    
                foreach ($user->unreadNotifications as $notification) {
                    FilamentNotification::make()
                    ->title($notification->data['title'] ?? 'Nova Notificação')
                    ->body($notification->data['content'] ?? 'Sem conteúdo')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('Ver')
                            ->button()
                            ->url($notification->data['url'] ?? '#', shouldOpenInNewTab: true) // Certifique-se de que a URL está correta
                    ])
                    ->send();
                
    
                    // Marcar como lida após exibição para evitar repetição
                    $notification->markAsRead();
                }
            }
        });
    }
}
