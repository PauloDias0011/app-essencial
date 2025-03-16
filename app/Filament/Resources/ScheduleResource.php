<?php

namespace App\Filament\Resources;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Checkbox;
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
use Filament\Infolists\Components\Sidebar;
use Filament\Infolists\Components\TableEntry;
use Filament\Infolists\Infolist;
use App\Models\ClassPlan;
use Filament\Infolists\Components\Split;
use Illuminate\Support\Facades\Auth;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationGroup = 'Agendamentos';
    protected static ?string $navigationLabel = 'Agendamentos';
    protected static ?string $label = 'Agendamento';
    protected static ?string $pluralLabel = 'Agendamentos';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Detalhes do Agendamento')
                    ->schema([
                        Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->relationship('professor', 'name')
                            ->required(),
                        Select::make('student_id')
                            ->label('🎓 Aluno')
                            ->relationship('student', 'name')
                            ->required(),
                        DateTimePicker::make('scheduled_at')
                            ->label('📅 Data e Hora')
                            ->required(),
                        Checkbox::make('is_recurring')
                            ->label('🔄 Agendamento Recorrente')
                            ->reactive(),
                        Select::make('recurrence_frequency')
                            ->label('⏳ Frequência de Recorrência')
                            ->options([
                                'daily' => 'Diário',
                                'weekly' => 'Semanal',
                                'monthly' => 'Mensal',
                            ])
                            ->visible(fn (callable $get) => $get('is_recurring') === true)
                            ->required(fn (callable $get) => $get('is_recurring') === true),
                    ]),
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('professor.name')->label('👨‍🏫 Professor')->sortable(),
                TextColumn::make('student.name')->label('🎓 Aluno')->sortable(),
                TextColumn::make('scheduled_at')
                    ->label('📅 Data e Hora')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('recurrence_frequency')
                    ->label('⏳ Frequência')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'daily' => 'Diariamente',
                        'weekly' => 'Semanalmente',
                        'monthly' => 'Mensalmente',
                        default => 'N/A',
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes do agendamento')
                    ->color('blue'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar este agendamento')
                    ->color('yellow'),
                DeleteAction::make()
                    ->label('Deletar')  
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover este agendamento')
                    ->color('red'),
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Professor')
                    ->relationship('professor', 'name'),
                SelectFilter::make('student_id')
                    ->label('🎓 Aluno')
                    ->relationship('student', 'name'),
            ], layout: FiltersLayout::AboveContent);
    }

     public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
          
                Section::make('📌 Informações do Agendamento')
                    ->schema([
                        TextEntry::make('scheduled_at')->label('📅 Data e Hora')->dateTime(),
                        TextEntry::make('recurrence_frequency')
                            ->label('⏳ Frequência')
                            ->formatStateUsing(fn($state) => match ($state) {
                                'daily' => 'Diariamente',
                                'weekly' => 'Semanalmente',
                                'monthly' => 'Mensalmente',
                                default => 'N/A',
                            }),
                    ]),
                Section::make('👨‍🏫 Informações do Professor')
                    ->schema([
                        TextEntry::make('professor.name')->label('Nome'),
                        TextEntry::make('professor.email')->label('Email'),
                        TextEntry::make('professor.phone')->label('Telefone')->default('Não informado'),
                    ]),
                Section::make('🎓 Informações do Aluno')
                    ->schema([
                        TextEntry::make('student.name')->label('Nome'),
                        TextEntry::make('student.email')->label('Email'),
                        TextEntry::make('student.phone')->label('Telefone')->default('Não informado'),
                        TextEntry::make('student.grade_year')->label('Série/Ano')->default('Não informado'),
                    ]),
          
        ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
            'view' => Pages\ViewSchedule::route('/{record}/id'),

        ];
    }
}
