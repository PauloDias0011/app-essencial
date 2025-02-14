<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?string $navigationLabel = 'Notas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('professor_id')
                ->relationship('professor', 'name')
                ->required()
                ->label('Professor'),
            Forms\Components\Select::make('student_id')
                ->relationship('student', 'name')
                ->required()
                ->label('Aluno'),
            Forms\Components\TextInput::make('subject')
                ->required()
                ->label('Disciplina'),
           
            Forms\Components\Select::make('semester')
                ->options([
                    '1' => '1º Semestre',
                    '2' => '2º Semestre',
                ])
                ->required()
                ->label('Semestre'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('professor.name')
                    ->label('Professor'),
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Aluno'),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Disciplina'),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Nota'),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semestre'),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}