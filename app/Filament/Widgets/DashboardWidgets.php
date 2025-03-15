<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;
use App\Models\Student;
use App\Models\Grade;


class DashboardWidgets extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total de Usuários', User::count())
                ->description('Número total de usuários cadastrados')
                ->icon('heroicon-o-users'),

            Card::make('Total de Alunos', Student::count())
                ->description('Número total de alunos matriculados')
                ->icon('heroicon-o-academic-cap'),

            Card::make('Média Geral das Notas', number_format(Grade::avg('grade'), 2))
                ->description('Média geral das notas dos alunos')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}