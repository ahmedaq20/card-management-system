<?php

namespace App\Observers;

use App\Models\DailySales;

class DailySalesObserver
{
    /**
     * Handle the DailySales "created" event.
     */
    public function created(DailySales $dailySales): void
    {
        $dailySales->seller->updateFinancialData();
    }

    /**
     * Handle the DailySales "updated" event.
     */
    public function updated(DailySales $dailySales): void
    {
        $dailySales->seller->updateFinancialData();
        
    }

    /**
     * Handle the DailySales "deleted" event.
     */
    public function deleted(DailySales $dailySales): void
    {
        $dailySales->seller->updateFinancialData();
        
    }

    /**
     * Handle the DailySales "restored" event.
     */
    public function restored(DailySales $dailySales): void
    {
        //
    }

    /**
     * Handle the DailySales "force deleted" event.
     */
    public function forceDeleted(DailySales $dailySales): void
    {
        //
    }
}