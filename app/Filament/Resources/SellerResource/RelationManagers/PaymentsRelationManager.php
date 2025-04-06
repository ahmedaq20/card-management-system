<?php

namespace App\Filament\Resources\SellerResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PaymentsRelationManager extends RelationManager
{    protected static ?string $recordTitleAttribute = 'amount';
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('PaymentsRelationManager')
            ->columns([
              
                    TextColumn::make('id')->label('رقم الدفعة')->sortable(),
                    TextColumn::make('amount')->label('المبلغ')->sortable(),
                    TextColumn::make('description')->label('الوصف'),
                    TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime(),
            ])
            ->filters([
                //
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