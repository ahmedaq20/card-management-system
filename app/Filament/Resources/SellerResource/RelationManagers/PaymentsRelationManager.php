<?php

namespace App\Filament\Resources\SellerResource\RelationManagers;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Tables\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PaymentsRelationManager extends RelationManager
{
    protected static ?string $recordTitleAttribute = 'amount';
    protected static ?string $label = 'المدفوعات';

    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('description')
                    ->label('الوصف')
                    ->maxLength(255),
                    DatePicker::make('date')
                    ->label('التاريخ') // Arabic: Date
                    ->default(Carbon::today())
                    ->required(), //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->recordTitleAttribute('PaymentsRelationManager')
            ->columns([

                // TextColumn::make('id')->label('رقم الدفعة')->sortable(),
                TextColumn::make('#')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                TextColumn::make('amount')->label('المبلغ')->sortable(),
                TextColumn::make('with_cards') // Use the accessor
                    ->label('نوع الدفعة')
                    ->badge()
                    ->money('ILS')
                    ->colors([
                        'success' => fn($state) => $state === 'مع بطاقات', // Green badge for "مع بطاقات"
                        'danger' => fn($state) => $state === 'بدون بطاقات', // Red badge for "بدون بطاقات"
                    ]),
                TextColumn::make('description')
                    ->label('الوصف')
                    ->formatStateUsing(fn($state) => $state ?? 'لا يوجد وصف'),
                TextColumn::make('date')->label('تاريخ الإضافة')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('all')
                    ->label('الكل')
                    ->query(fn(Builder $query) => $query), // No filtering, show all records

                Tables\Filters\Filter::make('with_cards')
                    ->label('مع بطاقات')
                    ->query(fn(Builder $query) => $query->where('with_cards', 1)),

                Tables\Filters\Filter::make('without_cards')
                    ->label('بدون بطاقات')
                    ->query(fn(Builder $query) => $query->where('with_cards', 0)),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
