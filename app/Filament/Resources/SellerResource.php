<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Seller;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToSelect;
use App\Filament\Resources\SellerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SellerResource\RelationManagers;
use Filament\Forms\Get;
use Filament\Forms\Set;

class SellerResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'البائعين';
    protected static ?string $navigationLabel = 'البائعين';

    public static function form(Form $form): Form
    {
        return $form

                ->schema([

                    TextInput::make('name')->label('اسم البائع')->required(),
                    TextInput::make('sales_point')->label('نقطة البيع')->required(),
                    TextInput::make('phone')->label('رقم الهاتف')->tel()->required()->columnSpan(2)->tel(),
                    Grid::make(2)->schema([
                        TextInput::make('cards_sold')
                        ->label('عدد البطاقات')
                        ->numeric()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            self::calculateRemainingDues($set, $get);
                        }),

                    TextInput::make('wholesale_price')
                        ->label('سعر الجملة')
                        ->numeric()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            self::calculateRemainingDues($set, $get);
                        }),

                    TextInput::make('amount_paid')
                        ->label('المبلغ المدفوع')
                        ->numeric()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            self::calculateRemainingDues($set, $get);
                        })
                        ->rule(function (Get $get) {
                            $total = floatval($get('cards_sold') ?? 0) * floatval($get('wholesale_price') ?? 0);
                            $paid = floatval($get('amount_paid') ?? 0);

                            if ($paid > $total) {
                                return 'المبلغ المدفوع أكبر من المستحقات المالية';
                            }
                            return null;
                        })
                    ->helperText(function (Get $get) {
                        $total = floatval($get('cards_sold') ?? 0) * floatval($get('wholesale_price') ?? 0);
                        $paid = floatval($get('amount_paid') ?? 0);
                        if ($paid > $total) {
                            return '⚠️ تنبيه: المبلغ المدفوع أكبر من المستحقات المالية';
                        }
                        return null;
                    }),

                        TextInput::make('remaining_dues')
                            ->label('المبلغ المتبقي')
                            ->numeric()
                            ->disabled() // حقل غير قابل للتعديل
                            ->dehydrated()
                            ->required(), // يتم حفظ القيمة في قاعدة البيانات
                    ]),

            ]);

    }

        private static function calculateRemainingDues(Set $set, Get $get): void
                  {
        $soldCards = floatval($get('cards_sold') ?? 0);
        $price = floatval($get('wholesale_price') ?? 0);
        $paid = floatval($get('amount_paid') ?? 0);
        $total = $soldCards * $price;

        if ($paid <= $total) {
            $set('remaining_dues', $total - $paid);
        } else {
            $set('remaining_dues', null);
        }
                  }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')->sortable()->searchable()->label('اسم البائع'),
            TextColumn::make('sales_point')->sortable()->searchable()->label('نقطة البيع'),
            TextColumn::make('phone')->label('رقم الهاتف'),
            TextColumn::make('cards_sold')->label('البطاقات المباعة'),
            TextColumn::make('amount_paid')->label('المبلغ المدفوع'),
            TextColumn::make('remaining_dues')->label('باقي المستحقات'),
            TextColumn::make('payments')->label('الدفعات'),
            TextColumn::make('wholesale_price')->label('سعر الجملة'),



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
            //
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
