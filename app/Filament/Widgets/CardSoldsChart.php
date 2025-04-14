<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CardSoldsChart extends ChartWidget
{
    protected static ?string $heading = 'عدد البطاقات المباعة يوميًا خلال آخر 30 يومًا';

    protected function getData(): array
    {
        // Fetch the number of cards sold per day over the last 30 days
        $data = DB::table('dailysales')
            ->selectRaw('DATE(updated_at) as date, SUM(quantity_sold) as total_quantity_sold')
            ->where('updated_at', '>=', now()->subDays(30))
            ->groupByRaw('DATE(updated_at)') // Use DATE(updated_at) in GROUP BY
            ->orderBy('date')
            ->get();

        // Prepare the data (totals)
        $totals = $data->pluck('total_quantity_sold')->toArray();

        // Generate labels as 1 to 30 days
        $labels = range(1, 30);

        return [
            'datasets' => [
                [
                    'label' => 'عدد البطاقات المباعة',
                    'data' => $totals,
                    'borderColor' => '#3B82F6', // Blue line
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // Light blue fill
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}