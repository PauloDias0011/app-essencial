<?php

use App\Models\Student;
use Filament\Widgets\PieChartWidget;

class StudentDistributionChart extends PieChartWidget
{
    protected static ?string $heading = 'Distribuição de Alunos por Série';

    protected function getData(): array
    {
        $series = Student::select('grade_year')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('grade_year')
            ->pluck('total', 'grade_year')
            ->toArray();

        return [
            'labels' => array_keys($series),
            'datasets' => [
                ['data' => array_values($series)],
            ],
        ];
    }
}