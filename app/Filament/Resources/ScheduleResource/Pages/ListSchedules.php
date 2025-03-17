<?php
namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Filament\Widgets\MyCalendarWidget;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Forms\Components\Accordion;
use Filament\Forms\Components\Section;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            MyCalendarWidget::class, // 🔹 O Calendário sempre aparece antes da tabela
        ];
    }

    public function getTable(): Table
    {
        return parent::getTable(); // 🔹 Mantém a tabela existente
    }

    
}
