<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Seller;
use Filament\Tables\Table;
use App\Models\FinancialPayment;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class FinancialDuesTable extends BaseWidget
{
    protected static ?string $heading = ' مستحقات البائعين';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Seller::query()->orderByDesc('remaining_dues')

            )
            ->columns([
                TextColumn::make('name')
                ->label('البائع'),

            TextColumn::make('remaining_dues')
                ->label('إجمالي المستحقات')
                ->money('ILS'),
            ]);
    }
}