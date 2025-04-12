<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\FinancialPayment;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class MonthlyPaymentsTable extends BaseWidget
{
    protected static ?string $heading = 'إجمالي الدفعات الشهرية';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FinancialPayment::query()
                    ->selectRaw('
                        DATE_FORMAT(MIN(created_at), "%Y-%m") as month,
                        SUM(amount) as total,
                        MIN(id) as id
                    ')
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            )
            ->columns([
                TextColumn::make('month')
                    ->label('الشهر') ->formatStateUsing(function ($state) {
                        $carbon = Carbon::createFromFormat('Y-m', $state);
                        return $carbon->translatedFormat('F Y'); // مثل: "مارس 2024"
                    }),

                TextColumn::make('total')
                    ->label('إجمالي الدفعات')
                    ->money('ILS'),
            ]);
            
    }

  
}