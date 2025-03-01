<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassPlanResource\Pages;
use App\Models\ClassPlan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step as WizardStep;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Infolists\Components\UrlEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ClassPlanResource extends Resource
{
    protected static ?string $model = ClassPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Planos de Aula';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                    WizardStep::make('Professor e Aluno')
                        ->schema([
                            Forms\Components\Select::make('professor_id')
                                ->relationship('professor', 'name')
                                ->required()
                                ->label('Professor'),
                            Forms\Components\Select::make('student_id')
                                ->relationship('student', 'name')
                                ->required()
                                ->label('Aluno'),
                        ]),
                    WizardStep::make('Upload do Arquivo')
                        ->schema([
                            Forms\Components\FileUpload::make('file_path')
                                ->required()
                                ->label('Plano de Aula (PDF ou DOCX)'),
                        ]),
                ])->columnSpan('full')
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                    wire:submit="register"
                >
                    Salvar
                </x-filament::button>
                BLADE)))
                
                    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('professor.name')->label('Professor')->sortable(),
                Tables\Columns\TextColumn::make('student.name')->label('Aluno')->sortable(),
                Tables\Columns\TextColumn::make('file_path')->label('Arquivo')->limit(20),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Data de Criação')
                ->dateTime('d/m/Y')
                ->sortable(),
            BadgeColumn::make('created_at_relative')
                ->label('')
                ->getStateUsing(fn ($record) => Carbon::parse($record->created_at)->diffForHumans())
                ->color(fn ($state) => match (true) {
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->relationship('professor', 'name')
                    ->label('Filtrar por Professor'),
                
                SelectFilter::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Filtrar por Aluno'),
                
                    Filter::make('created_from')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Criado a partir de')
                    ])
                    ->query(fn ($query, $data) => $query->when($data['created_from'], fn ($query) => $query->whereDate('created_at', '>=', Carbon::parse($data['created_from'])))),
                
                Filter::make('created_to')
                    ->form([
                        Forms\Components\DatePicker::make('created_to')->label('Criado até')
                    ])
                    ->query(fn ($query, $data) => $query->when($data['created_to'], fn ($query) => $query->whereDate('created_at', '<=', Carbon::parse($data['created_to'])))),
            ], layout: FiltersLayout::AboveContent);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('📌 Informações Gerais')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('professor.name')
                            ->label('👨‍🏫 Professor')
                            ->icon('heroicon-o-user')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('student.name')
                            ->label('🎓 Aluno')
                            ->icon('heroicon-o-user-group')
                            ->weight('bold')
                            ->color('primary'),
                    ]),

                Section::make('📄 Arquivo')
                    ->schema([
                        TextEntry::make('file_path')
                            ->label('📂 Nome do Arquivo')
                            ->icon('heroicon-o-document')
                            ->color('gray')
                            ->url(fn ($record) => asset('storage/' . $record->file_path), true),
                    ]),
            ]);
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
            'view' => Pages\ViewClassPlan::route('/{record}'),
        ];
    }
}
