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
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
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

    protected static ?string  $breadcrumb = ' المبيعات اليومية';
    // protected static ?string  $label = 'كشف المبيعات';
    protected static ?string  $pluralLabel = 'كشف  البيعات';

    
    
    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('seller_id')
            ->label('البائع')
            ->options(Seller::all()->pluck('name','id'))
            ->searchable()
            ->required()
            ->live()
            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                $seller = Seller::find($state);
                // dd($seller);
                if ($seller) {
                    $set('unit_price', $seller->wholesale_price);

                    // تحديث المجموع لو الكمية موجودة
                    $qty = $get('quantity_sold');
                    if ($qty) {
                        $set('total_amount', $qty * $seller->wholesale_price);
                    }
                }
            }),
            // BelongsToSelect::make('seller_id')
            // ->relationship('seller', 'name')
            // ->label('البائع')
            // ->searchable()
            // ->required()
            // ->live()
            // ->afterStateUpdated(function (Set $set, Get $get, $state) {
            //     $seller = Seller::find($state);
            //     // dd($seller);
            //     if ($seller) {
            //         $set('unit_price', $seller->wholesale_price);

            //         // تحديث المجموع لو الكمية موجودة
            //         $qty = $get('quantity_sold');
            //         if ($qty) {
            //             $set('total_amount', $qty * $seller->wholesale_price);
            //         }
            //     }
            // }),

        DatePicker::make('date')
            ->label('التاريخ')
            ->required()
            ->default(Carbon::today()),

        TextInput::make('quantity_sold')
            ->label('عدد البطاقات')
            ->numeric()
            ->required()
            ->live(onBlur: true)
            ->afterStateUpdated(function (Set $set, Get $get) {
                $qty = $get('quantity_sold');
                $unitPrice = $get('unit_price');

                if ($qty && $unitPrice) {
                    $set('total_amount', $qty * $unitPrice);
                }
            }),

        TextInput::make('unit_price')
            ->label('سعر الوحدة')
            ->numeric()
            ->reactive()
            ->disabled(), // فقط للعرض

            TextInput::make('amount_paid')
            ->label('المبلغ المحصل')
            ->required(),

        TextInput::make('total_amount')
            ->label('المجموع')
            ->disabled()
            ->default(0),


        Textarea::make('notes')
            ->label('الملاحظات')
            ->placeholder('اكتب ملاحظاتك هنا...')
            ->columnSpanFull()
            ->rows(4)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('date')->label('التاريخ')->sortable(),
                TextColumn::make('seller.name')->label('اسم البائع')->searchable(),
                TextColumn::make('quantity_sold')->label('عدد البطاقات')->sortable(),
                TextColumn::make('amount_paid')->label('المبلغ المحصل'),
                TextColumn::make('notes')->label('الملاحظات')->limit(50),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable(),
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


        // public static function infolist(Infolist $infolist): Infolist
        // {
        //     return $infolist
        //         ->schema([
        //     TextEntry::make('name')->label('اسم البائع'),
        //     TextEntry::make('sales_point')->label('نقطة البيع'),
        //     TextEntry::make('phone')->label('رقم الهاتف'),
        //     TextEntry::make('wholesale_price')->label('سعر الجملة'),
        //     TextEntry::make('cards_sold')->label('البطاقات المباعة'),
        //     TextEntry::make('amount_paid')->label('المبلغ المدفوع'),
        //     TextEntry::make('remaining_dues')->label('باقي المستحقات'),
        //     TextEntry::make('payments')->label('الدفعات'),
        //         ]);
        // }

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