<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\AnnouncementNotification;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        $announcement = $this->record;

        // Enviar notificações para as roles selecionadas
        if (!empty($announcement->recipient_roles)) {
            $users = User::role($announcement->recipient_roles)->get();
            Notification::send($users, new AnnouncementNotification($announcement));
        }

        // Enviar notificações para usuários específicos por e-mail
        if (!empty($announcement->recipient_emails)) {
            $users = User::whereIn('id', $announcement->recipient_emails)->get();
            Notification::send($users, new AnnouncementNotification($announcement));
        }
    }
}
