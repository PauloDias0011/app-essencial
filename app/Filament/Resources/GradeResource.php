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
use Illuminate\Support\Facades\Auth;

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
                Wizard\Step::make('Detalhes da Nota')
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
                            ->required(),
                        TextInput::make('subject')
                            ->label('📖 Disciplina')
                            ->required(),
                        TextInput::make('grade')
                            ->label('📊 Nota')
                            ->numeric()
                            ->required(),
                        Select::make('semester')
                            ->label('📆 Semestre')
                            ->options([
                                '1' => '1º Semestre',
                                '2' => '2º Semestre',
                            ])
                            ->required(),
                        TextInput::make('year')
                            ->label('📅 Ano Letivo')
                            ->required(),
                    ]),
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (Auth::user()->hasRole('Professor')) {
                    $query->where('professor_id', Auth::id());
                }
            })
            ->columns([
                TextColumn::make('subject')->label('📖 Disciplina')->sortable(),
                TextColumn::make('grade')->label('📊 Nota')->sortable(),
                TextColumn::make('semester')->label('📆 Semestre')->sortable(),
                TextColumn::make('year')->label('📅 Ano Letivo')->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes da nota')
                    ->color('blue'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar esta nota')
                    ->color('yellow'),
                DeleteAction::make()
                    ->label('Deletar')  
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover esta nota')
                    ->color('red'),
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Professor')
                    ->options(User::role('Professor')->pluck('name', 'id')),
                SelectFilter::make('semester')
                    ->label('📆 Semestre')
                    ->options([
                        '1' => '1º Semestre',
                        '2' => '2º Semestre',
                    ]),
            ], layout: FiltersLayout::AboveContent);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações da Nota')
                ->schema([
                    TextEntry::make('professor.name')->label('👨‍🏫 Professor'),
                    TextEntry::make('student.name')->label('🎓 Aluno'),
                    TextEntry::make('subject')->label('📖 Disciplina'),
                    TextEntry::make('grade')->label('📊 Nota'),
                    TextEntry::make('semester')->label('📆 Semestre'),
                    TextEntry::make('year')->label('📅 Ano Letivo'),
                ]),
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
}
