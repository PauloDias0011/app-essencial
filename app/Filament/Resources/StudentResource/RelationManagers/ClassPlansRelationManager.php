<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'classPlans';
    protected static bool $canCreate = true;
    protected static ?string $title = 'Planos de Aula';
    protected static ?string $modelLabel = 'Plano de Aula';
    protected static ?string $pluralModelLabel = 'Planos de Aula';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📝 Informações do Plano de Aula')
                    ->schema([
                        // ✅ Campo professor (preenchido automaticamente se for professor)
                        Forms\Components\Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->options(User::role('Professor')->pluck('name', 'id'))
                            ->default(Auth::user()->hasRole('Professor') ? Auth::id() : null)
                            ->disabled(Auth::user()->hasRole('Professor'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        // ✅ Campo aluno (preenchido automaticamente com o aluno atual)
                        Forms\Components\Select::make('student_id')
                            ->label('🎓 Aluno')
                            ->relationship('student', 'name')
                            ->default(fn ($livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->required(),

                        // ✅ Upload do arquivo do plano
                        Forms\Components\FileUpload::make('file_path')
                            ->label('📂 Arquivo do Plano de Aula')
                            ->disk('public')
                            ->directory('class-plans')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword', 
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'image/jpeg',
                                'image/png'
                            ])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->required()
                            ->helperText('Formatos aceitos: PDF, DOC, DOCX, JPG, PNG (máx. 10MB)')
                            ->columnSpanFull(),

                           
                    ])
                    ->columns(2),

                // ✅ Campo oculto para garantir student_id
                Forms\Components\Hidden::make('student_id')
                    ->default(fn ($livewire) => $livewire->ownerRecord->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([

                Tables\Columns\TextColumn::make('professor.name')
                    ->label('👨‍🏫 Professor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: Auth::user()->hasRole('Professor')),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('🗓️ Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at_relative')
                    ->label('⏳ Há quanto tempo')
                    ->formatStateUsing(fn($record) => Carbon::parse($record->created_at)->diffForHumans())
                    ->badge()
                    ->color(function ($record): string {
                        $diffInDays = Carbon::parse($record->created_at)->diffInDays(now());
                        return match (true) {
                            $diffInDays === 0 => 'success',
                            $diffInDays <= 7 => 'warning',
                            $diffInDays <= 30 => 'info',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\IconColumn::make('file_path')
                    ->label('📂 Arquivo')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document-x')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record): string => $record->file_path ? 'Arquivo anexado' : 'Sem arquivo'),
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Filtrar por Professor')
                    ->options(User::role('Professor')->pluck('name', 'id'))
                    ->visible(fn () => !Auth::user()->hasRole('Professor')),

            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('➕ Adicionar Plano de Aula')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->modalHeading('Adicionar Novo Plano de Aula')
                    ->modalWidth('2xl')
                    ->successNotificationTitle('Plano de aula adicionado com sucesso!')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Garantir que o professor_id seja preenchido
                        if (Auth::user()->hasRole('Professor')) {
                            $data['professor_id'] = Auth::id();
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('👁️ Visualizar')
                    ->color('info')
                    ->modalHeading('Detalhes do Plano de Aula')
                    ->modalWidth('2xl'),

                Tables\Actions\Action::make('download')
                    ->label('📥 Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record): string => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->file_path)),

                Tables\Actions\EditAction::make()
                    ->label('✏️ Editar')
                    ->color('warning')
                    ->modalHeading('Editar Plano de Aula')
                    ->modalWidth('2xl')
                    ->successNotificationTitle('Plano de aula atualizado com sucesso!'),

                Tables\Actions\DeleteAction::make()
                    ->label('🗑️ Excluir')
                    ->color('danger')
                    ->successNotificationTitle('Plano de aula removido com sucesso!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ✅ Ação em massa para alterar disciplina
                    Tables\Actions\BulkAction::make('changeSubject')
                        ->label('📖 Alterar Disciplina')
                        ->icon('heroicon-o-academic-cap')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('new_subject')
                                ->label('Nova Disciplina')
                                ->options([
                                    'Matemática' => 'Matemática',
                                    'Português' => 'Português',
                                    'História' => 'História',
                                    'Geografia' => 'Geografia',
                                    'Ciências' => 'Ciências',
                                    'Inglês' => 'Inglês',
                                    'Educação Física' => 'Educação Física',
                                    'Artes' => 'Artes',
                                    'Outros' => 'Outros',
                                ])
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['subject' => $data['new_subject']]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Alterar Disciplina dos Planos Selecionados')
                        ->modalDescription('Esta ação irá alterar a disciplina de todos os planos selecionados.')
                        ->successNotificationTitle('Disciplina dos planos alterada com sucesso!'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('🗑️ Excluir Selecionados')
                        ->successNotificationTitle('Planos de aula removidos com sucesso!'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Atualizar a cada 30 segundos
            ->emptyStateHeading('📭 Nenhum plano de aula encontrado')
            ->emptyStateDescription('Este aluno ainda não possui planos de aula cadastrados.')
            ->emptyStateIcon('heroicon-o-document-text');
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