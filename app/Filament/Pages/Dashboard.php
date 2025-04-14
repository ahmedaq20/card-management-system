<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;

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



}
