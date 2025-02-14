<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Alunos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Nome Completo'),
            Forms\Components\DatePicker::make('date_of_birth')
                ->required()
                ->label('Data de Nascimento'),
            Forms\Components\TextInput::make('grade_year')
                ->label('Série/Ano')
                ->required(),
            Forms\Components\Textarea::make('special_observations')
                ->label('Observações Especiais'),
            Forms\Components\Select::make('gender')
                ->options([
                    'M' => 'Masculino',
                    'F' => 'Feminino',
                ])
                ->label('Sexo')
                ->required(),
            Forms\Components\TextInput::make('school')
                ->label('Escola'),
            Forms\Components\Textarea::make('address')
                ->label('Endereço'),
            Forms\Components\Select::make('parent_id')
                ->relationship('parent', 'name')
                ->label('Pai/Responsável')
                ->searchable(),
            Forms\Components\Select::make('professor_id')
                ->relationship('professor', 'name')
                ->label('Professor')
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->label('Data de Nascimento'),
                Tables\Columns\TextColumn::make('grade_year')
                    ->label('Série/Ano'),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Pai/Responsável'),
                Tables\Columns\TextColumn::make('professor.name')
                    ->label('Professor'),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}