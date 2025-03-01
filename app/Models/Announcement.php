<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\AnnouncementNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'published_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($announcement) {
            // Obtém usuários das roles e remove duplicatas
            $users = \Spatie\Permission\Models\Role::whereIn('name', ['Professor', 'Pai/Responsavel'])
                ->with('users')
                ->get()
                ->pluck('users')
                ->flatten()
                ->unique('id'); // Garante que cada usuário receba apenas uma notificação
    
            // Só envia se ainda não existir no banco
            foreach ($users as $user) {
                if (!$user->notifications()->where('data->title', $announcement->title)->exists()) {
                    $user->notify(new AnnouncementNotification($announcement));
                }
            }
        });
    }
}
