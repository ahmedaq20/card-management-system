<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Pages\ViewSeller;
use App\Models\Seller;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FinancialPayment;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToSelect;
use App\Filament\Resources\SellerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FinancialPaymentResource;
use App\Filament\Resources\SellerResource\RelationManagers;

class SellerResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'البائعين';
    protected static ?string $navigationLabel = 'البائعين';

    protected static ?string  $breadcrumb = 'البائعين';
    // protected static ?string  $label = 'سجل البائعين';
    protected static ?string  $pluralLabel = 'سجل البائعين';

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    TextInput::make('name')->label('اسم البائع')->required(),
                    TextInput::make('sales_point')->label('نقطة البيع')->required(),
                    TextInput::make('phone')->label('رقم الهاتف')->tel()->required()->tel(),
                    TextInput::make('wholesale_price')
                    ->label('سعر الجملة')
                    ->numeric()
                    ->required(),

            ]);

    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                // TextColumn::make('id')->label('رقم البائع')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable()->label('اسم البائع'),
                TextColumn::make('sales_point')->sortable()->searchable()->label('نقطة البيع'),
                TextColumn::make('phone')->label('رقم الهاتف'),
                TextColumn::make('daily_sales.quantity_sold')
                ->label('إجمالي البطاقات المباعة')
                ->getStateUsing(function ($record) {
                    return $record->dailySales->sum('quantity_sold');
                }),

                TextColumn::make('daily_sales.amount_paid')
                ->label('إجمالي المبلغ المحصل')
                ->getStateUsing(function ($record) {
                    $totalPayments= FinancialPayment::where('seller_id',$record->id)->sum('amount');
                    return  (int)$totalPayments;

                }),
                TextColumn::make('remaining_dues_total')
                ->label('باقي المستحقات')
                ->getStateUsing(function ($record) {
                    $totalPayments= FinancialPayment::where('seller_id',$record->id)->sum('amount');

                    $totalQuantitySold = $record->dailySales->sum('quantity_sold');
                    $wholesalePrice = $record->wholesale_price;

                    if($totalQuantitySold > 0){
                        return ($totalQuantitySold * $wholesalePrice) - $totalPayments;
                    }
                    return 0;
                }),

                TextColumn::make('payments')
                ->label('الدفعات')
                ->counts('payments')
                ->icon('heroicon-o-currency-dollar')
                ->tooltip('عرض الدفعات'),

                // TextColumn::make('view_payments')
                // ->label('عرض الدفعات')
                // ->url(fn ($record) => route('filament.resources.sellers.edit', ['record' => $record->id]) . '#payments')
                // ->icon('heroicon-o-eye')
                // ->color('primary')
                // ->tooltip('عرض الدفعات'),

                TextColumn::make('wholesale_price')
                ->label('سعر الجملة')
                ->badge()
                ->color('success'),



            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),

        ];
    }
}