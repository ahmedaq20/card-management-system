<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CardSoldsChart extends ChartWidget
{
    protected static ?string $heading = 'عدد  البطاقات المباعة حسب الدفع';


    protected function getData(): array
    {
        $cardsWithPayment = DB::table('financial_payments')
            ->where('with_cards', true)
            ->count('with_cards');

        $cardsWithoutPayment = DB::table('financial_payments')
            ->where('with_cards', false)
            ->count('with_cards');

        return [
            'datasets' => [
                [
                    'data' => [$cardsWithPayment, $cardsWithoutPayment],
                    'backgroundColor' => ['#10B981', '#EF4444'], // أخضر وأحمر
                ],
            ],
            'labels' => ['مع دفعة', 'بدون دفعة'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}