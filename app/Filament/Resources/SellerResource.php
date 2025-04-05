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
