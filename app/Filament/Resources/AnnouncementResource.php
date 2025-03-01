<?php

namespace App\Filament\Resources;

use App\Models\Announcement;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements;
use App\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\ViewAnnouncement;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Infolists\Components\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;


class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Avisos/Comunicados';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                // 🟢 Passo 1: Informações principais
                Wizard\Step::make('Informações')
                    ->description('Preencha os dados principais')
                    ->schema([
                        Card::make([
                            TextInput::make('title')
                                ->label('Título')
                                ->placeholder('Digite o título...')
                                ->helperText('Esse título será exibido para os destinatários.')
                                ->suffixIcon('heroicon-o-pencil')
                                ->required(),
    
                            Textarea::make('content')
                                ->label('Conteúdo')
                                ->placeholder('Descreva o conteúdo aqui...')
                                ->helperText('Escreva uma descrição detalhada.')
                                ->autosize()
                                ->required(),
                        ]),
                    ]),
    
                // 🟡 Passo 2: Configurações de publicação
                Wizard\Step::make('Publicação')
                    ->description('Defina a data e os destinatários')
                    ->schema([
                        Card::make([
                            DateTimePicker::make('published_at')
                                ->label('Data de Publicação')
                                ->placeholder('Escolha a data...')
                                ->helperText('Escolha quando esse conteúdo será publicado.')
                                ->suffixIcon('heroicon-o-calendar')
                                ->required(),
    
                            Select::make('recipient_roles')
                                ->label('Roles Destinatárias')
                                ->multiple()
                                ->options([
                                    'admin' => '📌 Diretoria',
                                    'teacher' => '📚 Professor',
                                    'student' => '👨‍👩‍👧 Pai/Responsável',
                                ])
                                ->helperText('Escolha quais grupos terão acesso.')
                                ->required(),
                        ]),
                    ]),
    
                // 🔵 Passo 3: Destinatários específicos
                Wizard\Step::make('Destinatários')
                    ->description('Escolha os usuários específicos')
                    ->schema([
                        Card::make([
                            Select::make('recipient_emails')
                                ->label('Usuários Específicos')
                                ->multiple()
                                ->options(\App\Models\User::all()->pluck('email', 'id'))
                                ->searchable()
                                ->placeholder('Busque usuários pelo email...')
                                ->helperText('Envie notificações apenas para usuários específicos.')
                                ->suffixIcon('heroicon-o-user-group'),
                        ]),
                    ]),
            ])
                ->skippable() // Permite pular etapas
                ->extraAttributes(['class' => 'custom-wizard']) // Permite estilizar com CSS se necessário
                ->columnSpan('full')
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                    wire:submit="register"
                >
                    Salvar
                </x-filament::button>
                BLADE))),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('📌 Título')
                    ->searchable() // 🔍 Permite buscar pelo título
                    ->sortable(),  // 🔽 Adiciona ordenação
    
                TextColumn::make('published_at')
                    ->label('🗓️ Publicado em')
                    ->date('d/m/Y H:i') // 🕒 Formata data e hora
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->label('Excluir Selecionados')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->delete())
                    ->color('danger'),
            ])
            ->striped() // Adiciona zebra nas linhas
            ->defaultSort('published_at', 'desc') // Ordena por data mais recente
            ->paginated(10); // Define o número de itens por página
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('📌 Informações Gerais')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Título')
                            ->icon('heroicon-o-document-text')
                            ->weight('bold')
                            ->color('primary'),
    
                        TextEntry::make('published_at')
                            ->label('Publicado em')
                            ->icon('heroicon-o-calendar')
                            ->dateTime('d/m/Y H:i')
                            ->color('gray'),
                    ]),
    
                Section::make('📝 Conteúdo')
                    ->schema([
                        TextEntry::make('content')
                            ->label('Descrição')
                            ->markdown()
                            ->icon('heroicon-o-document'),
                    ]),
    
            ]);
    }

    
    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'view' => ViewAnnouncement::route('/{record}'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
