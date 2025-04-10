<?php

namespace App\Filament\Widgets;

use App\Models\FinancialPayment;
use App\Models\Seller;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('اجمال البائعين', Seller::count())
                ->description('عدد البائعين في النظام')
                ->descriptionIcon('heroicon-o-user',IconPosition::Before)
                ->chart([20,16,12,10,7,5,3,1])
                ->color('success'),


            Stat::make('البطاقات المباعة', Seller::sum('cards_sold'))
            ->description('اجمال البطاقات المباعة في النظام')
            ->descriptionIcon('heroicon-o-calendar-days',IconPosition::Before)
            ->chart([40,30,10,5,3,1])
            ->color('info'),

            Stat::make('الدفعات', FinancialPayment::sum('amount'))
            ->description('مجموع الدفعات في النظام')
            ->descriptionIcon('heroicon-o-currency-dollar',IconPosition::Before)
            ->chart([30,16,25,7,3])
            ->color(Color::Sky),
        ];

    }
}