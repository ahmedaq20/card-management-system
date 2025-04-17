<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Seller;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Models\FinancialPayment;
use Filament\Tables\Actions\Action;
use Tables\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\TableWidget as BaseWidget;

class FinancialDuesTable extends BaseWidget
{
    protected static ?string $heading = ' مستحقات البائعين';

    public function table(Table $table): Table
    {
        return $table
            ->poll('10s')
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


            ])
            ->actions([
                Action::make('add_payment')
                    ->label('إضافة دفعة')
                    ->icon('heroicon-s-plus')
                    ->color('primary')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('note')
                            ->label('ملاحظات'),
                            DatePicker::make('date')
                            ->label('التاريخ') // Arabic: Date
                            ->default(Carbon::today())
                            ->required(), //
          ])
                    ->action(function (array $data, Seller $record) {
                        \App\Models\FinancialPayment::create([
                            'seller_id' => $record->id,
                            'amount' => $data['amount'],
                            'description' => $data['note'] ?? null,
                            'date' => $data['date'],

                        ]);
                    })
                ]);
    }

   

}