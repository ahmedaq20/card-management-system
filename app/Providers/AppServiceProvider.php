<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Observers\DailySalesReportObserver;
use App\Models\DailySales;
use App\Models\FinancialPayment;
use App\Observers\PaymentObserver;
use App\Observers\DailySalesObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /*
         * Bootstrap any application services.
     */
    public function boot(): void
    {
        FinancialPayment::observe(PaymentObserver::class);
        DailySales::observe(DailySalesObserver::class);
    }
}