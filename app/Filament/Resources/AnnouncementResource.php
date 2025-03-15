<?php

namespace App\Filament\Resources;

use App\Models\Announcement;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
use App\Filament\Resources\AnnouncementResource\Pages;
use Filament\Tables\Enums\FiltersLayout;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Avisos';
    protected static ?string $label = 'Aviso';
    protected static ?string $pluralLabel = 'Avisos';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Detalhes do Aviso')->schema([
                    TextInput::make('title')
                        ->label('📌 Título')
                        ->placeholder('Digite o título do aviso')
                        ->required(),
                    Textarea::make('content')
                        ->label('📝 Conteúdo')
                        ->placeholder('Escreva o conteúdo do aviso')
                        ->autosize()
                        ->required(),
                ]),
                Wizard\Step::make('Configuração de Publicação')->schema([
                    DateTimePicker::make('published_at')
                        ->label('🗓️ Data de Publicação')
                        ->placeholder('Selecione a data')
                        ->required(),
                    Select::make('recipient_roles')
                        ->label('👥 Destinatários')
                        ->multiple()
                        ->options([
                            'admin' => '📌 Diretoria',
                            'teacher' => '📚 Professores',
                            'student' => '👨‍👩‍👧 Pais/Responsáveis',
                        ])
                        ->required(),
                ]),
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('📌 Título')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('🗓️ Publicado em')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y H:i'))
                    ->sortable(),
                BadgeColumn::make('recipient_roles')
                    ->label('👥 Destinatários')
                    ->formatStateUsing(fn ($state) => implode(', ', $state ?? []))
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes do aviso')
                    ->color('blue'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar este aviso')
                    ->color('yellow'),
                DeleteAction::make()
                    ->label('Deletar')  
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover este aviso')
                    ->color('red'),
            ])
            ->filters([
                SelectFilter::make('recipient_roles')
                    ->label('Destinatários')
                    ->options([
                        'admin' => 'Diretoria',
                        'teacher' => 'Professores',
                        'student' => 'Pais/Responsáveis',
                    ]),
                Filter::make('published_at')
                    ->label('Data de Publicação')
                    ->form([
                        DateTimePicker::make('published_at')
                            ->label('Filtrar por Data')
                    ])
                    ->query(fn ($query, array $data) => $query->when($data['published_at'], fn($query, $date) => $query->where('published_at', '>=', $date))),
            ],layout: FiltersLayout::AboveContent);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações do Aviso')
                ->schema([
                    TextEntry::make('title')->label('Título'),
                    TextEntry::make('published_at')->label('🗓️ Publicado em')->dateTime(),
                    TextEntry::make('recipient_roles')->label('👥 Destinatários')->formatStateUsing(fn ($state) => implode(', ', $state ?? [])),
                ]),
            Section::make('📝 Conteúdo')
                ->schema([
                    TextEntry::make('content')->label('Descrição')->markdown(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view' => Pages\ViewAnnouncement::route('/{record}'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
