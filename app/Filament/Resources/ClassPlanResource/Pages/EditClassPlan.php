<?php

namespace App\Filament\Resources\ClassPlanResource\Pages;

use App\Filament\Resources\ClassPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassPlan extends EditRecord
{
    protected static string $resource = ClassPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
