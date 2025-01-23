<?php

namespace App\Filament\Resources\ClassPlanResource\Pages;

use App\Filament\Resources\ClassPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassPlans extends ListRecords
{
    protected static string $resource = ClassPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
