<?php

namespace App\Filament\Widgets;

use Guava\Calendar\Widgets\CalendarWidget;
use App\Models\Schedule;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\Livewire;
use Illuminate\Support\Collection;
use Guava\Calendar\ValueObjects\Event;
use Illuminate\Support\Facades\Auth;
class MyCalendarWidget extends CalendarWidget
{
    protected string $calendarView = 'dayGridMonth';
    protected bool $eventClickEnabled = true;


  public function getEvents(array $fetchInfo = []): Collection | array
{
    $user = Auth::user();
    $panelId = Filament::getCurrentPanel()->getId();

    $query = Schedule::query()->whereBetween('scheduled_at', [
        $fetchInfo['start'],
        $fetchInfo['end'],
    ]);

    if ($panelId === 'teacher') {
        $query->where('professor_id', $user->id);
    }

    if ($panelId === 'parents') {
        $query->whereHas('student', function ($q) use ($user) {
            $q->where('parent_id', $user->id);
        });
    }

    return $query->get()->map(fn($schedule) => Event::make()
        ->key($schedule->id)
        ->title("📅 {$schedule->student->name} | Prof: {$schedule->professor->name}")
        ->start($schedule->scheduled_at)
        ->end($schedule->scheduled_at->copy()->addHour())
        ->url(route('filament.admin.resources.schedules.view', ['record' => $schedule->id]))
    );
}

    /**
     * Ação ao clicar em uma data para abrir o modal de criação.
     */
    public function onDateClick(array $info = []): void
    {
        if (!isset($info['dateStr'])) {
            return;
        }

        $this->dispatch('openModal', [
            'modal' => 'createSchedule',
            'data' => ['scheduled_at' => $info['dateStr']],
        ]);
    }

    /**
     * Ação ao clicar em um evento para abrir o modal de edição ou visualização.
     */
    public function onEventClick(array $info = [], ?string $action = null): void
    {
        \Log::info("Evento clicado!", $info); // 🔹 Isso vai testar se o clique está sendo reconhecido
    
        if (!isset($info['event']['id'])) {
            return;
        }
    
        $record = Schedule::find($info['event']['id']);
    
        if (!$record) {
            return;
        }
    
        // 🔹 Dispara um evento para abrir o modal no Filament
        $this->dispatch('openModal', [
            'modal' => 'viewSchedule',
            'data' => ['recordId' => $record->id],
        ]);
    }
    
 public function getHeaderActions(): array
{
    return [
        Action::make('createSchedule')
            ->label('➕ Adicionar Agendamento')
            ->color('primary')
            ->modalHeading('Criar Novo Agendamento')
            ->form($this->getFormSchema())
            ->action(fn ($data) => \App\Models\Schedule::create($data))
            ->visible(function () {
                $user = Auth::user();
                return $user->hasRole('Admin') || $user->hasRole('Professor');
            }),
    ];
}

    /**
     * Define o formulário de criação e edição do agendamento.
     */
    protected function getFormSchema(): array
    {
        return [
            DateTimePicker::make('scheduled_at')
                ->label('📅 Data e Hora')
                ->required(),
    
            Select::make('professor_id')
                ->label('👨‍🏫 Professor')
                ->options(fn () => \App\Models\User::whereHas('roles', fn ($query) => $query->where('name', 'Professor'))
                    ->pluck('name', 'id'))
                ->searchable()
                ->required(),
    
            Select::make('student_id')
                ->label('🎓 Aluno')
                ->options(fn () => \App\Models\Student::pluck('name', 'id'))
                ->searchable()
                ->required(),
    
            Checkbox::make('is_recurring')
                ->label('🔄 Agendamento Recorrente'),
    
            Select::make('recurrence_frequency')
                ->label('⏳ Frequência de Recorrência')
                ->options([
                    'daily' => 'Diário',
                    'weekly' => 'Semanal',
                    'monthly' => 'Mensal',
                ])
                ->visible(fn (callable $get) => $get('is_recurring') === true),
        ];
    }
    
}
