<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $label = 'Usuário';
    protected static ?string $pluralLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('📋 Informações Básicas')
                    ->schema([
                        TextInput::make('name')
                            ->label('👤 Nome Completo')
                            ->placeholder('Digite o nome completo')
                            ->required(),
                        TextInput::make('email')
                            ->label('📧 E-mail')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true // ✅ Esta linha resolve!
                            ),
                        TextInput::make('password')
                            ->label('🔑 Senha')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->visibleOn('create'),
                    ]),
                Wizard\Step::make('⚙️ Permissões')
                    ->schema([
                        Select::make('roles')
                            ->label('🎭 Função')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ]),
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('👤 Nome')->sortable(),
                TextColumn::make('email')->label('📧 E-mail')->sortable(),
                BadgeColumn::make('roles.name')
                    ->label('🎭 Função')
                    ->colors([
                        'primary' => 'Admin',
                        'success' => 'User',
                    ]),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('🎭 Filtrar por função')
                    ->relationship('roles', 'name'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes do usuário')
                    ->color('blue'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar este usuário')
                    ->color('yellow'),
                DeleteAction::make()
                    ->label('Deletar')
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover este usuário')
                    ->color('red'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações do Usuário')
                ->schema([
                    TextEntry::make('name')->label('👤 Nome'),
                    TextEntry::make('email')->label('📧 E-mail'),
                    TextEntry::make('roles.name')->label('🎭 Função'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
