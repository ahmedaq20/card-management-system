<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Seller;
use Filament\Tables\Table;
use App\Models\FinancialPayment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
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
                    // ->money('ILS')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => number_format( ($state ?? 0), 2) . ' ₪'),


                    TextColumn::make('payments')
                    ->label('الدفعات') // Arabic: View Payments
                    ->url(fn (Seller $record) => route('filament.admin.resources.sellers.edit', ['record' => $record->id]) . '#payments') // Redirect to the payments section
                    ->icon('heroicon-s-eye')
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => number_format( ($state ?? 0), 2) . ' ₪')
                    // Eye icon for the button
                    ->openUrlInNewTab(),

            ]);
    }
}