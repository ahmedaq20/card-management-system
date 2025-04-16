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
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
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
    protected static ?string $label = 'بائع'; // Singular Arabic title

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
                ->alignCenter()
                ->getStateUsing(function ($record) {
                    return $record->dailySales->sum('quantity_sold');
                }),

                TextColumn::make('daily_sales.amount_paid')
                ->label('إجمالي المبلغ المحصل')
                ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ₪')
                ->getStateUsing(function ($record) {
                    $totalPayments= FinancialPayment::where('seller_id',$record->id)->sum('amount');
                    return  (int)$totalPayments;

                }),
                TextColumn::make('remaining_dues_total')
                ->label('باقي المستحقات')
                ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ₪')
                ->getStateUsing(function ($record) {

                    $totalPayments= FinancialPayment::where('seller_id',$record->id)->sum('amount');

                    $totalQuantitySold = $record->dailySales->sum('quantity_sold');
                    $wholesalePrice = $record->wholesale_price;

                    if($totalQuantitySold > 0){
                        return ($totalQuantitySold * $wholesalePrice) - $totalPayments;
                    }elseif($totalQuantitySold == 0){
                        return  $totalPayments;
                    }
                    return 0;
                }),

                TextColumn::make('payments')
                ->label('الدفعات')
                ->counts('payments')
                // ->icon('heroicon-o-currency-dollar')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ₪')
                    ->alignCenter()
                ->tooltip('عرض الدفعات'),

                // TextColumn::make('view_payments')
                // ->label('عرض الدفعات')
                // ->url(fn ($record) => route('filament.resources.sellers.edit', ['record' => $record->id]) . '#payments')
                // ->icon('heroicon-o-eye')
                // ->color('primary')
                // ->tooltip('عرض الدفعات'),

                TextColumn::make('wholesale_price')
                ->label('سعر الجملة')

                    ->formatStateUsing(fn ($state) => number_format($state) . ' ₪')
                ->badge()
                ->alignCenter()
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
   public static function infolist(Infolist $infolist): Infolist
    {
       
        return $infolist
            ->schema([
                Tabs::make('Seller Details') // Main Tabs Component
                    ->columnSpanFull() // Ensures the tabs take up the full width of the modal
                    ->tabs([
                        // Tab: Basic Information
                        Tab::make('المعلومات الأساسية') // Arabic: Basic Information
                            ->icon('heroicon-o-user') // Icon for Basic Information
                            ->schema([
                                TextEntry::make('name')
                                    ->label('اسم البائع')
                                    ->extraAttributes(['class' => 'bg-gray-100 dark:bg-gray-800 p-2 rounded text-gray-900 dark:text-gray-100']),

                                TextEntry::make('sales_point')
                                    ->label('نقطة البيع')
                                    ->extraAttributes(['class' => 'bg-gray-100 dark:bg-gray-800 p-2 rounded text-gray-900 dark:text-gray-100']),
                            ])->columns(2),

                        // Tab: Contact Information
                        Tab::make('معلومات الاتصال') // Arabic: Contact Information
                            ->icon('heroicon-o-phone') // Icon for Contact Information
                            ->schema([
                                TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->extraAttributes(['class' => 'bg-gray-100 dark:bg-gray-800 p-2 rounded text-gray-900 dark:text-gray-100']),
                            ]),

                        // Tab: Sales and Financial Information
                        Tab::make('المبيعات والمالية') // Arabic: Sales and Financial Information
                            ->icon('heroicon-o-currency-dollar') // Icon for Sales and Financial Information
                            ->schema([
                                TextEntry::make('cards_sold')
                                    ->label('إجمالي البطاقات المباعة')
                                    ->badge()
                                    ->color('success') // Green badge for cards sold
                                    ->formatStateUsing(fn ($state) => $state ?? 0),

                                TextEntry::make('amount_paid')
                                    ->label('إجمالي المبلغ المحصل')
                                    ->badge()
                                    ->color('primary') // Blue badge for amount paid
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0) . ' ₪'),

                                TextEntry::make('remaining_dues')
                                    ->label('باقي المستحقات')
                                    ->badge()
                                    ->color('danger') // Red badge for remaining dues
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0) . ' ₪'),

                                TextEntry::make('payments')
                                    ->label('عدد الدفعات')
                                    ->badge()
                                    ->color('warning') // Yellow badge for payments
                                    ->formatStateUsing(fn ($state) => $state ?? 0),

                                TextEntry::make('wholesale_price')
                                    ->label('سعر الجملة')
                                    ->badge()
                                    ->color('info') // Light blue badge for wholesale price
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0) . ' ₪'),
                            ])->columns(3),

                        // Tab: Wholesale Information
                        Tab::make('معلومات الجملة') // Arabic: Wholesale Information
                            ->icon('heroicon-o-shopping-cart') // Icon for Wholesale Information
                            ->schema([
                                TextEntry::make('wholesale_price')
                                    ->label('سعر الجملة')
                                    ->badge()
                                    ->color('info') // Light blue badge for wholesale price
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0) . ' ₪'),
                            ]),
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