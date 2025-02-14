<?php

namespace App\Filament\Resources;

use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements;


class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Avisos/Comunicados';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('Título')->required(),
            Forms\Components\Textarea::make('content')->label('Conteúdo')->required(),
            Forms\Components\DatePicker::make('published_at')->label('Data de Publicação')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Título'),
            Tables\Columns\TextColumn::make('published_at')->date()->label('Publicado em'),
        ]);
    }

    public static function getPages(): array
{
    return [
        'index' => ListAnnouncements::route('/'),
    ];
}

}
