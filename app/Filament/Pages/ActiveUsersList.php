<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Collection;

class ActiveUsersList extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.active-users-list';
    protected static ?string $title = 'Active Users List';

    public function table(Table $table): Table
    {
        $data = collect();

        try {
            $client = new Client([
                'host' => env('MIKROTIK_HOST'),
                'user' => env('MIKROTIK_USERNAME'),
                'pass' => env('MIKROTIK_PASSWORD'),
                'port' =>(int) env('MIKROTIK_PORT',50001),
            ]);
            // استعلام المستخدمين النشطين
            $query = new Query('/ip/hotspot/active/print');
            $response = $client->query($query)->read();

            $data = collect($response);
        } catch (\Throwable $th) {
            // يمكنك تسجيل الخطأ هنا
        }

        return $table
        // ->query($data)
            ->columns([
                Tables\Columns\TextColumn::make('user')->label('اسم المستخدم'),
                Tables\Columns\TextColumn::make('address')->label('IP'),
                Tables\Columns\TextColumn::make('mac-address')->label('MAC'),
                Tables\Columns\TextColumn::make('uptime')->label('مدة الاتصال'),
                Tables\Columns\TextColumn::make('login-by')->label('الدخول عبر'),
            ])
            ->records($data); // ✅ استخدم records بدلاً من query
    }
}