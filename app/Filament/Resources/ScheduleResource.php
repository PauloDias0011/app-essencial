<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages\CreateSchedule;
use App\Filament\Resources\ScheduleResource\Pages\EditSchedule;
use App\Filament\Resources\ScheduleResource\Pages\ListSchedules;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Agendamentos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('professor_id')->relationship('professor', 'name')->searchable()->label('Professor')->required(),
            Forms\Components\Select::make('student_id')->relationship('student', 'name')->searchable()->label('Aluno')->required(),
            Forms\Components\DateTimePicker::make('scheduled_at')->label('Data e Hora')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('professor.name')->label('Professor'),
            Tables\Columns\TextColumn::make('student.name')->label('Aluno'),
            Tables\Columns\TextColumn::make('scheduled_at')->datetime()->label('Data e Hora'),
        ]);
    }
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
