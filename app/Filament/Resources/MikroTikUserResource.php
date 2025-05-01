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
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use RelationManagers\MikroTikUserRelationManager;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MikroTikUserResource\Pages;
use App\Filament\Resources\MikroTikUserResource\RelationManagers;
use App\Filament\Resources\MikrotikPaymentResource\RelationManagers\MikrotikPaymentRelationManager;

class MikroTikUserResource extends Resource
{
    protected static ?string $model = MikroTikUser::class;
  protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'مستخدمو MikroTik';
    protected static ?string $navigationGroup = 'إدارة الشبكة';
    protected static ?string $label = 'مستخدم ميكروتك';
    protected static ?string $pluralLabel = 'مستخدمون ميكروتك';
    protected static ?int $navigationSort = 5;

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

                    TextColumn::make('total_subscription_payment')
                    ->label('إجمالي المدفوعات')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                    return number_format($record->MikrotikPayment()->where('mikrotik_user_id',$record->id)->sum('amount')) . ' ₪';
                }),
                TextColumn::make('date_of_subscription')
                    ->label('تاريخ الاشتراك')
                    ->date(),
                    TextColumn::make('start_date_of_subscription')
                    ->label('بداية الاشتراك')
                    ->date(),
                    TextColumn::make('end_date_of_subscription')
                    ->label('نهاية الاشتراك')
                    ->date(),

                    BooleanColumn::make('subscription_status')
                    ->label('حالة الاشتراك')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                BooleanColumn::make('is_active')
                    ->label('نشط الآن')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('comment')
                    ->label('تعليق')
                    ->wrap(),

            ])
            ->defaultSort('date_of_subscription', 'desc')
            ->headerActions([
            //  CreateAction::make()
            //         ->label('إنشاء مستخدم جديد'),

                Action::make(name: 'refresh_all')
                    ->label('تحديث جميع البيانات من Mikrotik')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (Action $action) {
                        try {
                            $users = MikroTikUser::all();
                            $successCount = 0;
                            $errorCount = 0;

                            foreach ($users as $user) {
                                try {
                                    static::fetchFromApi($user);
                                    $successCount++;
                                } catch (\Exception $e) {
                                    $errorCount++;
                                }
                            }

                            Notification::make()
                                ->title('تم تحديث البيانات')
                                ->body("تم تحديث {$successCount} مستخدم بنجاح")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ في التحديث')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Action::make('Fetch from API')
                // ->label('تحديث')
                // ->icon('heroicon-o-arrow-path')
                // ->color('success')
                // ->action(function (MikroTikUser $record) {
                //     try {
                //         static::fetchFromApi($record); // Call the method to fetch data from the API


                //        Notification::make()
                //         ->title('تم تحديث البيانات بنجاح')
                //         ->success()
                //         ->send();


                //        } catch (\Exception $e) {
                //            Notification::make()
                //             ->title('فشل في تحديث البيانات')
                //             ->body($e->getMessage())
                //             ->danger()
                //             ->send();

                //         }
                // }),

                Action::make('renew_subscription')
                ->label('تجديد الاشتراك')
                ->icon('heroicon-o-calendar-days')
                ->color('primary')
                ->action(function (MikroTikUser $record) {

                    $record->update([
                        'start_date_of_subscription' => Carbon::now(),
                        'end_date_of_subscription' =>  Carbon::now()->addDays(30),
                    ]);

                  //  dd($record->start_date_of_subscription, $record->end_date_of_subscription);


                    Notification::make()
                        ->title('تم تجديد الاشتراك بنجاح')
                        ->success()
                        ->send();
                }),
                // ->requiresConfirmation()
                // ->color('success')
                // ->modalHeading('تحديث بيانات المستخدم')
            ])



            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

            ])
        ]);

    }

    public static function getRelations(): array
    {
        return [
            MikrotikPaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMikroTikUsers::route('/'),
            'create' => Pages\CreateMikroTikUser::route('/create'), // Use the custom Create page
            'edit' => Pages\EditMikroTikUser::route('/{record}/edit'),
        ];
    }

    public static function fetchFromApi(MikroTikUser $record): void
    {
        try {
            // Connect to MikroTik API
            $client = new Client([
                'host' => env('MIKROTIK_HOST'),
                'user' => env('MIKROTIK_USERNAME'),
                'pass' => env('MIKROTIK_PASSWORD'),
                'port' => (int) env('MIKROTIK_PORT', 8728),
            ]);

            // Query active users
            $query = new Query('/ip/hotspot/active/print');
            $activeUsers = $client->query($query)->read();

            // Find the user in the API response
            $userData = collect($activeUsers)->firstWhere('user', $record->user_in_network);
           //dd($activeUsers); // Debugging line to check the user data
            if ($userData) {
                // Update the record with API data
                $record->update([
                    'last_ip_address' => $userData['address'] ?? null,
                    'last_mac' => $userData['mac-address'] ?? null,
                    'is_active' => true,
                    'comment' => 'المستخدم نشط في الشبكة',
                    'user_in_network' => $record->user_in_network ?? null,
                ]);
            } else {
                // Mark the user as inactive if not found in the API
                $record->update([
                    'is_active' => false,
                    'comment' => 'المستخدم غير نشط في الشبكة',
                ]);
            }
        } catch (\Throwable $e) {
            // Handle API connection errors
            throw new \Exception('فشل في جلب البيانات من MikroTik: ' . $e->getMessage());
        }
    }
}
