<?php

namespace App\Filament\Resources;


use Filament\Forms;
use Filament\Tables;
use App\Models\Seller;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\DailySales;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Models\DailySalesReport;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\BelongsToSelect;
use App\Filament\Resources\DailySalesReportResource\Pages;
use App\Filament\Resources\DailySalesReportResource\RelationManagers;
use App\Filament\Resources\DailySalesReportResource\Pages\EditDailySalesReport;
use App\Filament\Resources\DailySalesReportResource\Pages\ListDailySalesReports;
use App\Filament\Resources\DailySalesReportResource\Pages\CreateDailySalesReport;

class DailySalesReportResource extends Resource
{
    protected static ?string $model = DailySales::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'المبيعات اليومية';
    protected static ?string $navigationLabel = 'كشف المبيعات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            BelongsToSelect::make('seller_id')
                ->relationship('seller', 'name')
                ->label('البائع')
                ->searchable()
                ->required(),
        
            DatePicker::make('date')
                ->label('التاريخ')
                ->required()
                ->default(Carbon::today()), // Set default to today's date
        
            TextInput::make('quantity_sold')
                ->label('عدد البطاقات')
                ->numeric()
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, Get $get) {
                    self::calculateTotalAmount($set, $get);
                }),
        
            TextInput::make('unit_price')
                ->label('سعر الوحدة')
                ->default(function (Get $get) {
                    $sellerId = $get('seller_id');
                    if ($sellerId) {
                        $seller = Seller::find($sellerId);
                        return $seller ? $seller->unit_price : null; // جلب سعر الوحدة من جدول البائعين
                    }
                    return null;
                })
                ->disabled(), // تعيين سعر الوحدة بناءً على البائع
        
            TextInput::make('total_amount') // حقل المجموع
                ->label('المجموع')
                ->disabled() // لا يمكن تعديله
                ->default(0), // القيمة الافتراضية للمجموع تكون 0
        
            TextInput::make('amount_paid')
                ->label('المبلغ المحصل')
                ->required(),
        
            Textarea::make('notes')
                ->label('الملاحظات')
                ->placeholder('اكتب ملاحظاتك هنا...')
                ->columnSpanFull()
                ->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('التاريخ')->sortable(),
                TextColumn::make('seller.name')->label('اسم البائع')->sortable()->searchable(),
                TextColumn::make('sold_amount')->label('المبلغ المباع'),
                TextColumn::make('collected_amount')->label('المبلغ المحصل'),
                TextColumn::make('remaining')->label('المتبقي'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function calculateTotalAmount(Set $set, Get $get): void
    {
        $quantitySold = floatval($get('quantity_sold') ?? 0);
        $unitPrice = floatval($get('unit_price') ?? 0);
        $totalAmount = $quantitySold * $unitPrice; // حساب المجموع

        $set('total_amount', $totalAmount); // تحديث حقل المجموع
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailySalesReports::route('/'),
            'create' => Pages\CreateDailySalesReport::route('/create'),
            'edit' => Pages\EditDailySalesReport::route('/{record}/edit'),
        ];
    }
}