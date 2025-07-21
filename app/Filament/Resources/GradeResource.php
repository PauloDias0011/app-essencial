<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
use App\Models\User;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;
    protected static ?string $navigationGroup = 'Educação';
    protected static ?string $navigationLabel = 'Notas';
    protected static ?string $label = 'Nota';
    protected static ?string $pluralLabel = 'Notas';
    
    public static function form(Form $form): Form   
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('📝 Detalhes da Nota')
                    ->schema([
                        Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->options(User::role('Professor')->pluck('name', 'id'))
                            ->default(Auth::user()->hasRole('Professor') ? Auth::id() : null)
                            ->disabled(Auth::user()->hasRole('Professor'))
                            ->required(),

                        Select::make('student_id')
                            ->label('🎓 Aluno')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('subject')
                            ->label('📖 Disciplina')
                            ->placeholder('Ex: Matemática, Português, História')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('grade')
                            ->label('📊 Nota')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->step(0.1)
                            ->placeholder('0.0 a 10.0')
                            ->required(),

                        // ✅ Campo MÊS ao invés de semestre
                        Select::make('month')
                            ->label('📅 Mês')
                            ->options([
                                1 => 'Janeiro',
                                2 => 'Fevereiro',
                                3 => 'Março',
                                4 => 'Abril',
                                5 => 'Maio',
                                6 => 'Junho',
                                7 => 'Julho',
                                8 => 'Agosto',
                                9 => 'Setembro',
                                10 => 'Outubro',
                                11 => 'Novembro',
                                12 => 'Dezembro',
                            ])
                            ->default(now()->month) // ✅ Mês atual como padrão
                            ->required(),

                        TextInput::make('year')
                            ->label('📅 Ano Letivo')
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2030)
                            ->default(now()->year) // ✅ Ano atual como padrão
                            ->required(),
                    ]),
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // ✅ Professores só veem suas próprias notas
                if (Auth::user()->hasRole('Professor')) {
                    $query->where('professor_id', Auth::id());
                }
                
                // ✅ Ordenar por ano e mês mais recentes
                $query->orderBy('year', 'desc')->orderBy('month', 'desc');
            })
            ->columns([
                TextColumn::make('student.name')
                    ->label('🎓 Aluno')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('📖 Disciplina')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grade')
                    ->label('📊 Nota')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning', 
                        default => 'danger',
                    }),

                TextColumn::make('month')
                    ->label('📅 Mês')
                    ->formatStateUsing(fn (int $state): string => [
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                    ][$state] ?? '')
                    ->sortable(),

                TextColumn::make('year')
                    ->label('📅 Ano')
                    ->sortable(),

                TextColumn::make('professor.name')
                    ->label('👨‍🏫 Professor')
                    ->toggleable(isToggledHiddenByDefault: Auth::user()->hasRole('Professor')),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes da nota'),

                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar esta nota'),

                DeleteAction::make()
                    ->label('Deletar')  
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover esta nota')
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Professor')
                    ->options(User::role('Professor')->pluck('name', 'id'))
                    ->visible(fn () => !Auth::user()->hasRole('Professor')), // ✅ Só Admin vê este filtro

                SelectFilter::make('student_id')
                    ->label('🎓 Aluno')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('subject')
                    ->label('📖 Disciplina')
                    ->options(function () {
                        return Grade::distinct()
                            ->pluck('subject', 'subject')
                            ->filter()
                            ->toArray();
                    }),

                // ✅ Filtro por MÊS
                SelectFilter::make('month')
                    ->label('📅 Mês')
                    ->options([
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                    ]),

                SelectFilter::make('year')
                    ->label('📅 Ano')
                    ->options(function () {
                        return Grade::distinct()
                            ->pluck('year', 'year')
                            ->sort()
                            ->toArray();
                    }),

            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('📭 Nenhuma nota encontrada')
            ->emptyStateDescription('Ainda não existem notas cadastradas.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações da Nota')
                ->schema([
                    TextEntry::make('professor.name')
                        ->label('👨‍🏫 Professor'),

                    TextEntry::make('student.name')
                        ->label('🎓 Aluno'),

                    TextEntry::make('subject')
                        ->label('📖 Disciplina'),

                    TextEntry::make('grade')
                        ->label('📊 Nota')
                        ->badge()
                        ->color(fn (string $state): string => match (true) {
                            $state >= 8 => 'success',
                            $state >= 6 => 'warning',
                            default => 'danger',
                        }),

                    // ✅ Exibir mês em português
                    TextEntry::make('month')
                        ->label('📅 Mês')
                        ->formatStateUsing(fn (int $state): string => [
                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                        ][$state] ?? ''),

                    TextEntry::make('year')
                        ->label('📅 Ano Letivo'),

                    TextEntry::make('created_at')
                        ->label('📅 Criado em')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
            'view' => Pages\ViewGrade::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();
    $panelId = Filament::getCurrentPanel()->getId();

    // Painel dos pais/responsáveis: mostrar apenas os planos dos filhos
    if ($panelId === 'parents') {
        return $query->whereHas('student', function ($q) use ($user) {
            $q->where('parent_id', $user->id);
        });
    }

    // Painel dos professores: mostrar apenas os planos do próprio professor
    if ($panelId === 'teacher') {
        return $query->where('professor_id', $user->id);
    }

    // Admin vê tudo
    return $query;
}
    
}