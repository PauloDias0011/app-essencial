<?php

use App\Models\Grade;
use Filament\Widgets\LineChartWidget;

class GradeTrendChart extends LineChartWidget
{
    protected static ?string $heading = 'Média Geral das Notas por Semestre';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $semesters = [
            '1º Semestre' => Grade::where('semester', '1')->avg('grade') ?? 0,
            '2º Semestre' => Grade::where('semester', '2')->avg('grade') ?? 0,
        ];

        return [
            'labels' => array_keys($semesters),
            'datasets' => [
                ['label' => 'Média Geral', 'data' => array_values($semesters)],
            ],
        ];
    }
}