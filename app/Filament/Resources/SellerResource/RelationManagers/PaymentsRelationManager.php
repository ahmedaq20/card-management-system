<?php

namespace App\Filament\Resources\SellerResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Tables\Tabs\Tab;
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
                    TextColumn::make('with_cards') // Use the accessor
                    ->label('نوع الدفعة')
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => $state === 'مع بطاقات', // Green badge for "مع بطاقات"
                        'danger' => fn ($state) => $state === 'بدون بطاقات', // Red badge for "بدون بطاقات"
                    ]),
                    TextColumn::make('description')
                    ->label('الوصف')
                    ->formatStateUsing(fn ($state) => $state ?? 'لا يوجد وصف'),
                    TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('all')
                ->label('الكل')
                ->query(fn (Builder $query) => $query), // No filtering, show all records

            Tables\Filters\Filter::make('with_cards')
                ->label('مع بطاقات')
                ->query(fn (Builder $query) => $query->where('with_cards', 1)),

            Tables\Filters\Filter::make('without_cards')
                ->label('بدون بطاقات')
                ->query(fn (Builder $query) => $query->where('with_cards', 0)),
                
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