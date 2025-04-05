<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DailySalesReport;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\BelongsToSelect;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DailySalesReportResource\Pages;
use App\Filament\Resources\DailySalesReportResource\RelationManagers;
use App\Filament\Resources\DailySalesReportResource\Pages\EditDailySalesReport;
use App\Filament\Resources\DailySalesReportResource\Pages\ListDailySalesReports;
use App\Filament\Resources\DailySalesReportResource\Pages\CreateDailySalesReport;
use App\Models\DailySales;
use Filament\Forms\Components\Textarea;

class DailySalesReportResource extends Resource
{
    protected static ?string $model = DailySales::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'المبيعات اليومية';
    protected static ?string $navigationLabel = 'كشف المبيعات';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            BelongsToSelect::make('seller_id')
            ->relationship('seller', 'name')
            ->label('البائع')
            ->searchable()
            ->required(),
            DatePicker::make('date')->label('التاريخ')->required(),
            TextInput::make('sold_amount')->label('المبلغ المباع')->numeric()->required(),
            TextInput::make('collected_amount')->label('المبلغ المحصل')->numeric()->required(),
            TextInput::make('remaining')->label('المتبقي')->numeric()->required(),
            Textarea::make('notes')
            ->label('الملاحظات')
            ->placeholder('اكتب ملاحظاتك هنا...')
            ->columnSpanFull()
            ->rows(4),
            /*
             Grid::make(2)->schema([
                TextInput::make('sold_cards')
                    ->label('عدد البطاقات')
                    ->numeric()
                    ->required()
                    ->live(), // لتحديث القيم مباشرة

                TextInput::make('wholesale_price')
                    ->label('سعر الجملة')
                    ->numeric()
                    ->required()
                    ->live(),

                TextInput::make('amount_paid')
                    ->label('المبلغ المدفوع')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $total = $get('sold_cards') * $get('wholesale_price');
                        $remaining = $total - $get('amount_paid');
                        $set('remaining_dues', $remaining);
                    }),

                TextInput::make('remaining_dues')
                    ->label('المبلغ المتبقي')
                    ->numeric()
                    ->disabled(), // عدم التعديل عليه يدويًا
            ]),
        ]);
            */

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
                TextColumn::make('remaining')->label('المتبقي'),



                // TextColumn::make('seller.name')->label('اسم البائع')->sortable()->searchable(),

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