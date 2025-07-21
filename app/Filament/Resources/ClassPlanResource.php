<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassPlanResource\Pages;
use App\Models\ClassPlan;
use Carbon\Carbon;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class ClassPlanResource extends Resource
{
    protected static ?string $model = ClassPlan::class;
    protected static ?string $navigationGroup = 'Educação';
    protected static ?string $navigationLabel = 'Planos de Aula';
    protected static ?string $label = 'Plano de Aula';
    protected static ?string $pluralLabel = 'Planos de Aula';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Dados do Plano')
                    ->schema([
                        Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->relationship(
                                name: 'professor',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->role('Professor')
                            )
                            ->required(),
                        Select::make('student_id')
                            ->label('🎓 Aluno')
                            ->relationship('student', 'name')
                            ->required(),
                    ]),
                Wizard\Step::make('Upload do Arquivo')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('📂 Plano de Aula')
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->helperText('Somente arquivos PDF ou DOCX.'),
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
                TextColumn::make('created_at')
                    ->label('🗓️ Criado em')
                    ->dateTime()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->sortable(),
                BadgeColumn::make('created_at_relative')
                    ->label('⏳ Há quanto tempo')
                    ->getStateUsing(fn($record) => Carbon::parse($record->created_at)->diffForHumans())
                    ->color(fn($state) => match (true) {
                        str_contains($state, 'segundo') => 'gray',
                        str_contains($state, 'minuto') => 'gray',
                        str_contains($state, 'hora') => 'gray',
                        str_contains($state, 'ontem') => 'yellow',
                        str_contains($state, 'semana') => 'blue',
                        str_contains($state, 'mês') => 'green',
                        default => 'gray',
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes do plano de aula'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar este plano de aula'),
                DeleteAction::make()
                    ->label('Deletar')
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover este plano de aula')
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Professor')
                    ->relationship('professor', 'name'),
                SelectFilter::make('student_id')
                    ->label('🎓 Aluno')
                    ->relationship('student', 'name'),
                Filter::make('created_from')
                    ->label('Criado a partir de')
                    ->form([
                        DatePicker::make('created_from')
                    ])
                    ->query(fn($query, $data) => $query->when($data['created_from'], fn($query) => $query->whereDate('created_at', '>=', Carbon::parse($data['created_from'])))),
                Filter::make('created_to')
                    ->label('Criado até')
                    ->form([
                        DatePicker::make('created_to')
                    ])
                    ->query(fn($query, $data) => $query->when($data['created_to'], fn($query) => $query->whereDate('created_at', '<=', Carbon::parse($data['created_to'])))),
            ], layout: FiltersLayout::AboveContent) ->emptyStateHeading('📭 Nenhum plano de aula encontrado')
            ->emptyStateDescription('Ainda não existem planos de aula cadastrados.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações do Plano')
                ->schema([
                    TextEntry::make('professor.name')->label('👨‍🏫 Professor'),
                    TextEntry::make('student.name')->label('🎓 Aluno'),
                    TextEntry::make('created_at')->label('🗓️ Criado em')->dateTime(),
                ]),
            Section::make('📂 Arquivo')
                ->schema([
                    TextEntry::make('file_path')->label('📄 Documento')->url(fn($record) => asset('storage/' . $record->file_path), true),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassPlans::route('/'),
            'create' => Pages\CreateClassPlan::route('/create'),
            'edit' => Pages\EditClassPlan::route('/{record}/edit'),
            'view' => Pages\ViewClassPlan::route('/{record}'),
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
