<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Schedule;
use Carbon\Carbon;

class SchedulesCalendar extends Widget
{
    protected static ?string $heading = '📅 Calendário de Agendamentos';
    
    protected static string $view = 'filament.widgets.schedules-calendar';
    protected static ?int $sort = 6;


    public function getViewData(): array
    {
        $events = Schedule::query()
            ->where('scheduled_at', '>=', Carbon::now()->startOfMonth()) // Carrega os eventos do mês atual
            ->get()
            ->map(fn ($schedule) => [
                'title' => "{$schedule->student->name} - {$schedule->professor->name}",
                'start' => $schedule->scheduled_at->toIso8601String(),
                'url' => route('filament.admin.resources.schedules.view', $schedule),
            ])
            ->toArray();

        return compact('events');
    }
}
