<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Schedule;
use Carbon\Carbon;

class UpcomingSchedulesCalendar extends BaseWidget
{
    protected static ?string $heading = '🗓️ Próximos Agendamentos';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Schedule::query()
                    ->whereBetween('scheduled_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                    ->orderBy('scheduled_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('professor.name')->label('👨‍🏫 Professor'),
                TextColumn::make('student.name')->label('🎓 Aluno'),
                TextColumn::make('scheduled_at')->label('📅 Data e Hora')->dateTime('d/m/Y H:i'),
            ]);
    }
}