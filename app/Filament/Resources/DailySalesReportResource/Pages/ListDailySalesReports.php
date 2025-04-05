<?php

namespace App\Filament\Resources\DailySalesReportResource\Pages;

use App\Filament\Resources\DailySalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailySalesReports extends ListRecords
{
    protected static string $resource = DailySalesReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
