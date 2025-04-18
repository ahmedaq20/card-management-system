<?php

namespace App\Filament\Resources\DailySalesReportResource\Pages;

use Filament\Actions;
use App\Models\DailySales;
use App\Models\FinancialPayment;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\DailySalesReportResource;

class CreateDailySalesReport extends CreateRecord
{
    protected static string $resource = DailySalesReportResource::class;

    protected function handleRecordCreation(array $data): DailySales
    {
        // Save the record in the primary table
        $dailySales = DailySales::create($data);
     
        // Save the same record in another table
        FinancialPayment::create([
            'seller_id' => $data['seller_id'],
            'amount' => $data['amount_paid'],
            'with_cards' => 1,
            'description' => $data['notes'],
            'daily_sales_id' => $dailySales->id,
        ]);

        return $dailySales;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }




}