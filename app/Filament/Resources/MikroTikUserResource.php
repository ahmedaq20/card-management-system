<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use RouterOS\Query;
use Filament\Tables;
use RouterOS\Client;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MikroTikUser;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MikroTikUserResource\Pages;
use App\Filament\Resources\MikroTikUserResource\RelationManagers;

class MikroTikUserResource extends Resource
{
    protected static ?string $model = MikroTikUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'مستخدمو MikroTik';
    protected static ?string $navigationGroup = 'إدارة الشبكة';
    protected static ?string $label = 'مستخدم ميكروتك';
    protected static ?string $pluralLabel = 'مستخدمون ميكروتك';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->label('اسم المستخدم')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->maxLength(15)
                    ->nullable(),

                TextInput::make('user_in_network')
                    ->label('اسم المستخدم في الشبكة')
                    ->required()
                    ->maxLength(255),

                TextInput::make('password_in_network')
                    ->label('كلمة المرور في الشبكة')
                    ->password()
                    ->required()
                    ->maxLength(255),

                DatePicker::make('date_of_subscription')
                    ->label('تاريخ الاشتراك')
                    ->default(now())
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
                TextColumn::make('username')
                    ->label('اسم المستخدم')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user_in_network')
                    ->label('اسم المستخدم في الشبكة'),

                TextColumn::make('last_ip_address')
                    ->label('آخر عنوان IP'),

                TextColumn::make('last_mac')
                    ->label('آخر عنوان MAC'),

                TextColumn::make('date_of_subscription')
                    ->label('تاريخ الاشتراك')
                    ->date(),

                BooleanColumn::make('is_active')
                    ->label('نشط الآن')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('comment')
                    ->label('تعليق')
                    ->wrap(),
            ])
            ->defaultSort('date_of_subscription', 'desc')
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMikroTikUsers::route('/'),
            'create' => Pages\CreateMikroTikUser::route('/create'), // Use the custom Create page
            'edit' => Pages\EditMikroTikUser::route('/{record}/edit'),
        ];
    }
}