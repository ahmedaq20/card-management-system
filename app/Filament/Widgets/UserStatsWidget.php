<?php

namespace App\Filament\Widgets;

use App\Models\Seller;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class UserStatsWidget extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('اجمال البائعين', Seller::count())
                ->description('عدد البائعين في النظام')
                ->descriptionIcon('heroicon-o-user',IconPosition::Before)
                ->chart([20,16,12,10,7,5,3,1])
                ->color('success'),
        ];
        
    }
}

