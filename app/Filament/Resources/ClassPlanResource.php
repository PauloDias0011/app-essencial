<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassPlanResource\Pages;
use App\Models\ClassPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClassPlanResource extends Resource
{
    protected static ?string $model = ClassPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Planos de Aula';

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
            Forms\Components\FileUpload::make('file_path')
                ->required()
                ->label('Plano de Aula (PDF ou DOCX)'),
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
                Tables\Columns\TextColumn::make('file_path')
                    ->label('Arquivo'),
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
            'index' => Pages\ListClassPlans::route('/'),
            'create' => Pages\CreateClassPlan::route('/create'),
            'edit' => Pages\EditClassPlan::route('/{record}/edit'),
        ];
    }
}