<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\GradesRelationManager;
use App\Models\Student;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationGroup = 'Educação';
    protected static ?string $navigationLabel = 'Alunos';
    protected static ?string $label = 'Aluno';
    protected static ?string $pluralLabel = 'Alunos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('📋 Dados Pessoais')
                    ->schema([
                        TextInput::make('name')
                            ->label('👤 Nome Completo')
                            ->placeholder('Digite o nome completo')
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->label('📅 Data de Nascimento')
                            ->required(),
                        Select::make('gender')
                            ->label('⚧️ Sexo')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Feminino',
                            ])
                            ->required(),
                    ]),
                Wizard\Step::make('🏫 Informações Escolares')
                    ->schema([
                        TextInput::make('grade_year')
                            ->label('📖 Série/Ano')
                            ->required(),
                        TextInput::make('school')
                            ->label('🏫 Escola'),
                        Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->relationship(
                                name: 'professor',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->role('Professor')
                            )
                            ->required(),
                    ]),
                Wizard\Step::make('🏡 Informações Adicionais')
                    ->schema([
                        Select::make('parent_id')
                            ->label('👨‍👩‍👧‍👦 Pai/Responsável')
                            ->relationship(
                                name: 'professor',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->role('Pai/Responsavel')
                            )
                            ->required(),
                        Textarea::make('address')
                            ->label('📍 Endereço')
                            ->placeholder('Rua, número, bairro, cidade'),
                        Textarea::make('special_observations')
                            ->label('📝 Observações Especiais')
                            ->placeholder('Adicione qualquer observação relevante sobre o aluno'),
                    ]),
            ])->columnSpanFull()
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('👤 Nome')->sortable(),
                TextColumn::make('date_of_birth')->label('📅 Data de Nascimento')->date()->sortable(),
                TextColumn::make('grade_year')->label('📖 Série/Ano')->sortable(),
                TextColumn::make('parent.name')->label('👨‍👩‍👧‍👦 Pai/Responsável'),
                TextColumn::make('professor.name')->label('👨‍🏫 Professor')->sortable(),
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Professor')
                    ->relationship('professor', 'name'),
            ], layout: FiltersLayout::AboveContent);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações do Aluno')
                ->schema([
                    TextEntry::make('name')->label('👤 Nome'),
                    TextEntry::make('date_of_birth')->label('📅 Data de Nascimento')->dateTime('d/m/Y'),
                    TextEntry::make('grade_year')->label('📖 Série/Ano'),
                    TextEntry::make('school')->label('🏫 Escola'),
                    TextEntry::make('parent.name')->label('👨‍👩‍👧‍👦 Pai/Responsável'),
                    TextEntry::make('professor.name')->label('👨‍🏫 Professor'),
                    TextEntry::make('address')->label('🏠 Endereço'),

                ]),
            Section::make('📖 Boletim Escolar')
                ->schema([
                    TextEntry::make('grades_summary')
                        ->label('📚 Notas por Disciplina')
                        ->formatStateUsing(fn($record) => $record->grades->groupBy('semester')->map(fn($semester) => $semester->map(fn($grade) => "{$grade->subject}: {$grade->grade}")->join(', '))->join(' | ')),
                ]),
            Section::make('📄 Planos de Aula')
                ->schema([
                    TextEntry::make('classPlans_summary')
                        ->label('📄 Planos de Aula')
                        ->formatStateUsing(fn($record) => $record->classPlans->map(fn($plan) => "{$plan->professor->name} ({$plan->created_at->format('d/m/Y')})")->join(', ')),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            GradesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }
}
