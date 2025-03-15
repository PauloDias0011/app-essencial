<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';
    protected static bool $canCreate = true; // Garante que pode criar novas notas

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('subject')
                ->required()
                ->label('Disciplina'),
            Forms\Components\TextInput::make('grade')
                ->numeric()
                ->required()
                ->suffixIcon('heroicon-o-academic-cap')
                ->label('Nota'),
            Forms\Components\Select::make('semester')
                ->options([
                    '1' => '1º Semestre',
                    '2' => '2º Semestre',
                ])
                ->required()
                ->label('Semestre'),
            Forms\Components\Select::make('year')
                ->options([
                    '2024' => '2024',
                    '2023' => '2023',
                    '2022' => '2022',
                ])
                ->required()
                ->label('Ano Letivo'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')->label('Disciplina')->sortable(),
                Tables\Columns\TextColumn::make('grade')->label('Nota')->sortable(),
                Tables\Columns\TextColumn::make('semester')->label('Semestre')->sortable(),
                Tables\Columns\TextColumn::make('year')->label('Ano Letivo')->sortable(),
            ])
            ->filters([
                SelectFilter::make('semester')
                    ->options([
                        '1' => '1º Semestre',
                        '2' => '2º Semestre',
                    ])
                    ->label('Filtrar por Semestre'),
                SelectFilter::make('year')
                    ->options([
                        '2024' => '2024',
                        '2023' => '2023',
                        '2022' => '2022',
                    ])
                    ->label('Filtrar por Ano Letivo'),
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()->label('Adicionar Nota')->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
