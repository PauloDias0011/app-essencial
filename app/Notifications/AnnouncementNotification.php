<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Announcement;

class AnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable)
    {
        return ['database']; // Armazena a notificação no banco
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->announcement->title,
            'content' => $this->announcement->content,
            'published_at' => $this->announcement->published_at,
        ];
    }
    public function toArray($notifiable)
{
    return [
        'title'   => $this->announcement->title,
        'content' => $this->announcement->content,
        'url'     => route('filament.admin.resources.announcements.view', $this->announcement->id), // Verifique se esta rota existe
    ];
}

}
