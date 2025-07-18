<?php

namespace App\Filament\Resources\MikrotikPaymentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MikrotikPaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'MikrotikPayment';
     protected static ?string $recordTitleAttribute = 'description';
    protected static ?string $label = 'مدفوعات مستخدم ميكروتك';
    protected static ?string $pluralLabel = 'مدفوعات مستخدم ميكروتك';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                ->label('المبلغ')
                ->required()
                ->numeric(),

            Forms\Components\DatePicker::make('payment_date')
                ->label('تاريخ الدفع')
                ->required()
                ->default(now())
                ->date()
              ,

            Forms\Components\TextInput::make('description')
                ->label('الوصف')
                ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('المدفوعات')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                ->label('المبلغ')
                ->sortable(),

            Tables\Columns\TextColumn::make('payment_date')
                ->label('تاريخ الدفع')
                ->date()
                ->default(now()),

            Tables\Columns\TextColumn::make('description')
                ->label('الوصف'),
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