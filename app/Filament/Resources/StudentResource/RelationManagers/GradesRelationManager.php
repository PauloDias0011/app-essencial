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
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';
    protected static bool $canCreate = true;
    protected static ?string $title = 'Notas do Aluno';
    protected static ?string $modelLabel = 'Nota';
    protected static ?string $pluralModelLabel = 'Notas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📝 Informações da Nota')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('📖 Disciplina')
                            ->placeholder('Ex: Matemática, Português, História')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('grade')
                            ->label('📊 Nota')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->step(0.1)
                            ->placeholder('0.0 a 10.0')
                            ->suffixIcon('heroicon-o-academic-cap')
                            ->required()
                            ->rules(['numeric', 'min:0', 'max:10']),

                        // ✅ Campo MÊS ao invés de semestre
                        Forms\Components\Select::make('month')
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
                            ->default(now()->month)
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('year')
                            ->label('📅 Ano Letivo')
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2030)
                            ->default(now()->year)
                            ->required()
                            ->rules(['numeric', 'min:2020', 'max:2030']),

                        // ✅ Campo professor (preenchido automaticamente se for professor)
                        Forms\Components\Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->options(User::role('Professor')->pluck('name', 'id'))
                            ->default(Auth::user()->hasRole('Professor') ? Auth::id() : null)
                            ->disabled(Auth::user()->hasRole('Professor'))
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                // ✅ Validação para evitar notas duplicadas
                Forms\Components\Hidden::make('student_id')
                    ->default(fn ($livewire) => $livewire->ownerRecord->id)
                    ->rules([
                        function (Get $get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $exists = \App\Models\Grade::where([
                                    'student_id' => $value,
                                    'subject' => $get('subject'),
                                    'month' => $get('month'),
                                    'year' => $get('year'),
                                ])
                                ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                ->exists();

                                if ($exists) {
                                    $fail('Já existe uma nota para esta disciplina no mês selecionado.');
                                }
                            };
                        },
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('📖 Disciplina')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('grade')
                    ->label('📊 Nota')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => number_format($state, 1)),

                // ✅ Coluna MÊS com nomes em português
                Tables\Columns\TextColumn::make('month')
                    ->label('📅 Mês')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => [
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                    ][$state] ?? ''),

                Tables\Columns\TextColumn::make('year')
                    ->label('📅 Ano')
                    ->sortable(),

                Tables\Columns\TextColumn::make('professor.name')
                    ->label('👨‍🏫 Professor')
                    ->toggleable(isToggledHiddenByDefault: Auth::user()->hasRole('Professor'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Filtro por MÊS
                SelectFilter::make('month')
                    ->label('📅 Filtrar por Mês')
                    ->options([
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                    ])
                    ->multiple(),

                SelectFilter::make('year')
                    ->label('📅 Filtrar por Ano')
                    ->options(function () {
                        return \App\Models\Grade::distinct()
                            ->pluck('year', 'year')
                            ->sort()
                            ->toArray();
                    })
                    ->multiple(),

                SelectFilter::make('subject')
                    ->label('📖 Filtrar por Disciplina')
                    ->options(function () {
                        return \App\Models\Grade::distinct()
                            ->pluck('subject', 'subject')
                            ->filter()
                            ->sort()
                            ->toArray();
                    })
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('grade_range')
                    ->label('📊 Filtrar por Faixa de Nota')
                    ->options([
                        'excellent' => '🟢 Excelente (8.0 - 10.0)',
                        'good' => '🟡 Bom (6.0 - 7.9)',
                        'poor' => '🔴 Precisa Melhorar (0.0 - 5.9)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                in_array('excellent', $data['values'] ?? []),
                                fn (Builder $query): Builder => $query->orWhere('grade', '>=', 8),
                            )
                            ->when(
                                in_array('good', $data['values'] ?? []),
                                fn (Builder $query): Builder => $query->orWhereBetween('grade', [6, 7.9]),
                            )
                            ->when(
                                in_array('poor', $data['values'] ?? []),
                                fn (Builder $query): Builder => $query->orWhere('grade', '<', 6),
                            );
                    }),

            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('➕ Adicionar Nota')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->modalHeading('Adicionar Nova Nota')
                    ->successNotificationTitle('Nota adicionada com sucesso!')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Garantir que o professor_id seja preenchido
                        if (Auth::user()->hasRole('Professor')) {
                            $data['professor_id'] = Auth::id();
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('✏️ Editar')
                    ->color('warning')
                    ->modalHeading('Editar Nota')
                    ->successNotificationTitle('Nota atualizada com sucesso!'),

                Tables\Actions\ViewAction::make()
                    ->label('👁️ Visualizar')
                    ->color('info')
                    ->modalHeading('Detalhes da Nota'),

                Tables\Actions\DeleteAction::make()
                    ->label('🗑️ Excluir')
                    ->color('danger')
                    ->successNotificationTitle('Nota removida com sucesso!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ✅ Ação em massa para alterar mês
                    Tables\Actions\BulkAction::make('changeMonth')
                        ->label('📅 Alterar Mês')
                        ->icon('heroicon-o-calendar')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('new_month')
                                ->label('Novo Mês')
                                ->options([
                                    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                                    4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                                    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                                    10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                                ])
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['month' => $data['new_month']]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Alterar Mês das Notas Selecionadas')
                        ->modalDescription('Esta ação irá alterar o mês de todas as notas selecionadas.')
                        ->successNotificationTitle('Mês das notas alterado com sucesso!'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('🗑️ Excluir Selecionadas')
                        ->successNotificationTitle('Notas removidas com sucesso!'),
                ]),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc')
            ->poll('30s') // Atualizar a cada 30 segundos
            ->emptyStateHeading('📭 Nenhuma nota encontrada')
            ->emptyStateDescription('Este aluno ainda não possui notas cadastradas.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

   

    // ✅ Verificar permissões (métodos não-estáticos)
    public function canCreate(): bool
    {
        return Auth::user()->hasRole(['Super Admin', 'Professor']);
    }

    public function canEdit($record): bool
    {
        return Auth::user()->hasRole(['Super Admin', 'Professor']);
    }

    public function canDelete($record): bool
    {
        return Auth::user()->hasRole(['Super Admin', 'Professor']);
    }

    public function canDeleteAny(): bool
    {
        return Auth::user()->hasRole(['Super Admin', 'Professor']);
    }
}