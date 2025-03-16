<?php

use App\Models\Expense;
use Filament\Widgets\BarChartWidget;

class MonthlyExpensesChart extends BarChartWidget
{
    protected static ?string $heading = 'Total de Despesas por Mês';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $expenses = Expense::selectRaw("DATE_TRUNC('month', date_expense) as month, SUM(total_cost) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return [
            'labels' => array_keys($expenses),
            'datasets' => [
                ['label' => 'Total Gasto', 'data' => array_values($expenses)],
            ],
        ];
    }
}