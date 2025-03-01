<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages\ViewExpense;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step as WizardStep;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationLabel = 'Despesas';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                    WizardStep::make('Detalhes da Despesa')
                        ->schema([
                            Select::make('professor_id')
                            ->label('Professor')
                            ->options(User::role('Professor')->pluck('name', 'id'))
                            ->default(Auth::user()->hasRole('Professor') ? Auth::id() : null)
                            ->disabled(Auth::user()->hasRole('Professor'))
                            ->helperText('Selecione um professor se necessário'),
                            Forms\Components\TextInput::make('details')
                                ->required()
                                ->placeholder('Ex.: Compra de material')
                                ->helperText('Descreva a despesa')
                                ->suffixIcon('heroicon-o-pencil')
                                ->label('Descrição'),
                            Forms\Components\TextInput::make('total_cost')
                                ->numeric()
                                ->required()
                                ->placeholder('Ex.: 100,00')
                                ->suffixIcon('heroicon-o-tag')
                                ->helperText('Informe o valor da despesa')
                                ->label('Valor'),
                            Forms\Components\DatePicker::make('date_expense')
                                ->required()
                                ->helperText('Informe a data da despesa')
                                ->label('Data'),
                        ]),
                ])
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
            ->modifyQueryUsing(function ($query) {
                if (Auth::user()->hasRole('Professor')) {
                    $query->where('professor_id', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('professor.name')->label('Professor')->sortable(),
                Tables\Columns\TextColumn::make('details')->label('Descrição')->sortable(),
                Tables\Columns\TextColumn::make('total_cost')->label('Valor')->sortable()->money('BRL'),
                Tables\Columns\TextColumn::make('date_expense')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                BadgeColumn::make('date_relative')
                    ->label('')
                    ->getStateUsing(fn ($record) => Carbon::parse($record->date_expense)->diffForHumans(now()))
                    ->color(fn ($record) => self::getDateBadgeColor($record->date_expense)),
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
                    ->options(User::role('Professor')->pluck('name', 'id'))
                    ->label('Professor'),
                
                Filter::make('date_from')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->label('Data a partir de')
                    ])
                    ->query(fn ($query, $data) => $query->when($data['date_from'], fn ($query) => $query->whereDate('date_expense', '>=', Carbon::parse($data['date_from'])))),
                
                Filter::make('date_to')
                    ->form([
                        Forms\Components\DatePicker::make('date_to')->label('Data até')
                    ])
                    ->query(fn ($query, $data) => $query->when($data['date_to'], fn ($query) => $query->whereDate('date_expense', '<=', Carbon::parse($data['date_to'])))),
                    ],layout: FiltersLayout::AboveContent);
    }

    private static function getDateBadgeColor($date)
    {
        $diff = Carbon::parse($date)->diffInDays(Carbon::now());
        
        return match (true) {
            $diff === 0 => 'success', // Hoje
            $diff === 1 => 'warning', // Ontem
            $diff <= 7 => 'info', // Última semana
            $diff <= 30 => 'blue', // Último mês
            default => 'gray', // Mais de um mês
        };
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('📌 Informações da Despesa')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('professor.name')->label('📚  Professor')->weight('bold')->color('primary'),
                        TextEntry::make('details')->label('📝 Descrição')->weight('bold')->color('primary'),
                        TextEntry::make('total_cost')->label('💰 Valor')->weight('bold')->color('primary')->money('BRL'),
                        TextEntry::make('date_expense')
                            ->label('📆 Data')
                            ->color('gray')
                            ->dateTime('d/m/Y'),
                    ]),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
            'view' => ViewExpense::route('/{record}'),
        ];
    }
}
