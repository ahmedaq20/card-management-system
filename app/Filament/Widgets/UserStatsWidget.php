<?php

namespace App\Filament\Widgets;

use RouterOS\Query;
use RouterOS\Client;
use App\Models\Seller;
use PHPUnit\Exception;
use App\Models\DailySales;
use App\Models\FinancialPayment;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStatsWidget extends BaseWidget
{

    protected function getStats(): array
    {
        $activeUserCount = 0;
        try {
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
          }
          catch(\Throwable $e) {
            $activeUserCount = 0;
            Log::error('MikroTik connection or query failed: ' . $e->getMessage());

          }

        // حساب عدد البطاقات المباعة خلال الشهر الحالي
          $soldCardsThisMonth = DailySales::whereMonth('date', now()->month) // Current month
            ->whereYear('created_at', now()->year) // Current year
            ->sum('quantity_sold');

            // $amountPaidForSoldsThisMonth = DailySales::whereMonth('date', now()->month) // Current month
            // ->whereYear('date', now()->year) // Current year
            // ->sum('amount_paid');

            // إضافة الدفعات المالية خلال الشهر الحالي
            $financialPaymentsThisMonth = FinancialPayment::whereMonth('created_at', now()->month) // Current month
            ->whereYear('created_at', now()->year) // Current year
            ->sum('amount');

    // //إجمالي البطاقات المباعة والدفعات المالية
    //  $totalSoldAndPaymentsThisMonth = $amountPaidForSoldsThisMonth + $financialPaymentsThisMonth;
    
        return [
            // Stat::make('اجمال البائعين', Seller::count())
            //     ->description('عدد البائعين في النظام')
            //     ->descriptionIcon('heroicon-o-user',IconPosition::Before)
            //     ->chart([20,16,12,10,7,5,3,1])
            //     ->color('success'),
            // Clients From API
            Stat::make('العملاء النشطين', $activeUserCount)
            ->description('عدد العملاء المتصلين عبر MikroTik')
            ->descriptionIcon('heroicon-o-wifi',IconPosition::Before)
            ->chart([100, 250, 300, 500, 700])
            ->color('success'),

            Stat::make('البطاقات المباعة خلال الشهر', $soldCardsThisMonth)
            ->description('عدد البطاقات المباعة في الشهر الحالي')
            ->descriptionIcon('heroicon-o-calendar-days', IconPosition::Before)
            ->chart([40, 30, 10, 5, 3, 1])
            ->color('info'),

             Stat::make('مبيعات البطاقات خلال الشهر', $financialPaymentsThisMonth)
            ->description('مجموع مبيعات البطاقات خلال الشهر الحالي ')
            ->descriptionIcon('heroicon-o-calendar-days', IconPosition::Before)
            ->chart([40, 30, 10, 5, 3, 1])
            ->color('primary'),

            Stat::make('البطاقات المباعة', Seller::sum('cards_sold'))
            ->description('اجمال البطاقات المباعة في النظام')
            ->descriptionIcon('heroicon-o-calendar-days',IconPosition::Before)
            ->chart([40,30,10,5,3,1])
            ->color('info'),

            Stat::make('الدفعات', FinancialPayment::sum('amount'))
            ->description('مجموع الدفعات في النظام')
            ->descriptionIcon('heroicon-o-currency-dollar',IconPosition::Before)
            ->chart([30,16,25,7,3])
            ->color(Color::Sky),
            
            
        ];

    }

    protected function getPollingInterval(): ?string
    {
        return '10s';
    }
}