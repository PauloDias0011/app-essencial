<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ClassPlan;

class RecentClassPlansTable extends BaseWidget
{

    protected static ?string $heading = '📄 Últimos Planos de Aula';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(ClassPlan::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('professor.name')->label('👨‍🏫 Professor'),
                TextColumn::make('file_path')->label('📄 Documento')->limit(30),
                TextColumn::make('created_at')->label('📅 Criado em')->dateTime('d/m/Y H:i'),
            ]);
    }
}
