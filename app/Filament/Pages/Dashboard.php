<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Widgets\Grid;
use App\Filament\Widgets\CardSoldsChart;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationGroup = 'لوحة التحكم';
    protected static ?int $navigationSort = -2;
    protected static ?string $title = 'لوحة التحكم';
    protected static ?string $slug = 'dashboard';
    protected static ?string $navigationLabel = 'لوحة التحكم';


    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    // public function getHeaderWidgets(): array
    // {
    //     return [
    //         Grid::make()
    //             ->columns(1) // 🟢 يجعلها صف واحد = تأخذ كامل الشاشة
    //             ->schema([
    //                 CardSoldsChart::class,
    //             ]),
    //     ];
    // }

    protected function getPollingInterval(): ?string
    {
        return '10s'; // Refresh the dashboard every 10 seconds
    }


}
