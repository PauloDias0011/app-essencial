<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Facades\Auth;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Despesas';
    protected static ?string $label = 'Despesa';
    protected static ?string $pluralLabel = 'Despesas';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Detalhes da Despesa')
                    ->schema([
                        Select::make('professor_id')
                            ->label('👨‍🏫 Professor')
                            ->options(User::role('Professor')->pluck('name', 'id'))
                            ->default(Auth::user()->hasRole('Professor') ? Auth::id() : null)
                            ->disabled(Auth::user()->hasRole('Professor'))
                            ->required(),
                        TextInput::make('details')
                            ->label('📝 Descrição')
                            ->placeholder('Ex.: Compra de material')
                            ->required(),
                        TextInput::make('total_cost')
                            ->label('💰 Valor')
                            ->numeric()
                            ->required(),
                        DatePicker::make('date_expense')
                            ->label('📅 Data')
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
                TextColumn::make('professor.name')->label('👨‍🏫 Professor')->sortable(),
                TextColumn::make('details')->label('📝 Descrição')->sortable(),
                TextColumn::make('total_cost')->label('💰 Valor')->sortable()->money('BRL'),
                TextColumn::make('date_expense')
                    ->label('📅 Data')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->sortable(),
                BadgeColumn::make('date_relative')
                    ->label('⏳ Há quanto tempo')
                    ->getStateUsing(fn ($record) => Carbon::parse($record->date_expense)->diffForHumans())
                    ->color(fn ($record) => self::getDateBadgeColor($record->date_expense)),
                ToggleColumn::make('is_reimbursed')
                ->label('🧾 Reembolsado')


            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Ver detalhes da despesa'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Editar esta despesa'),
                DeleteAction::make()
                    ->label('Deletar')  
                    ->icon('heroicon-o-trash')
                    ->tooltip('Remover esta despesa')
            ])
            ->filters([
                SelectFilter::make('professor_id')
                    ->label('👨‍🏫 Professor')
                    ->options(User::role('Professor')->pluck('name', 'id')),
                Filter::make('date_from')
                    ->label('Data a partir de')
                    ->form([
                        DatePicker::make('date_from')
                    ])
                    ->query(fn ($query, $data) => $query->when($data['date_from'], fn ($query) => $query->whereDate('date_expense', '>=', Carbon::parse($data['date_from'])))),
                Filter::make('date_to')
                    ->label('Data até')
                    ->form([
                        DatePicker::make('date_to')
                    ])
                    ->query(fn ($query, $data) => $query->when($data['date_to'], fn ($query) => $query->whereDate('date_expense', '<=', Carbon::parse($data['date_to'])))),
            ], layout: FiltersLayout::AboveContent)->emptyStateHeading('📭 Nenhuma despesa  encontrada')
            ->emptyStateDescription('Ainda não existem despesas cadastradas.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    private static function getDateBadgeColor($date)
    {
        $diff = Carbon::parse($date)->diffInDays(Carbon::now());
        return match (true) {
            $diff === 0 => 'success',
            $diff === 1 => 'warning',
            $diff <= 7 => 'info',
            $diff <= 30 => 'blue',
            default => 'gray',
        };
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('📌 Informações da Despesa')
                ->schema([
                    TextEntry::make('professor.name')->label('👨‍🏫 Professor'),
                    TextEntry::make('details')->label('📝 Descrição'),
                    TextEntry::make('total_cost')->label('💰 Valor')->money('BRL'),
                    TextEntry::make('date_expense')->label('📅 Data')->dateTime(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
            'view' => Pages\ViewExpense::route('/{record}'),
        ];
    }
}
