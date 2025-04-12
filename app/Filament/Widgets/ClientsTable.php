<?php

namespace App\Filament\Widgets;

use RouterOS\Query;
use RouterOS\Client;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ClientsTable extends BaseWidget
{
    protected function getStats(): array
    {
        // الاتصال بـ MikroTik
        $client = new Client([
            'host' => env('MIKROTIK_HOST'),
            'user' => env('MIKROTIK_USERNAME'),
            'pass' => env('MIKROTIK_PASSWORD'),
            'port' =>(int) env('MIKROTIK_PORT',50001),
        ]);

        // استعلام المستخدمين النشطين
        $query = new Query('/ip/hotspot/active/print');
        $activeUsers = $client->query($query)->read();

        // حساب عددهم
        $activeUserCount = count($activeUsers);

        return [
            Stat::make('العملاء النشطين', $activeUserCount)
                ->description('عدد العملاء المتصلين عبر MikroTik')
                ->descriptionIcon('heroicon-o-wifi')
                ->color('success'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '10s';
    }
}