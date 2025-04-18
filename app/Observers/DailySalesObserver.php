<?php

namespace App\Observers;

use App\Models\DailySales;
use App\Models\FinancialPayment;

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

        // i'am using it in DailySales model in boot methods
    //  // Check if the amount_paid field was updated
    //  if ($dailySales->isDirty('amount_paid')) {
    //     // Update or create a corresponding record in FinancialPayments
    //     FinancialPayment::updateOrCreate(
    //         [
    //             'seller_id' => $dailySales->seller_id,
    //             'date' => $dailySales->date,
    //         ],
    //         [
    //             'amount' => $dailySales->amount_paid,
    //             'description' => 'Updated from Daily Sales Report',
    //         ]
    //     );
    // }
        
    }

    /**
     * Handle the DailySales "deleted" event.
     */
    public function deleted(DailySales $dailySales): void
    {
        $financialPayment = FinancialPayment::where('daily_sales_id', $dailySales->id) // Match the specific DailySales record
        ->first(); // جلب أول سجل فقط

    // حذف الدفعة إذا تم العثور عليها
         if ($financialPayment) {
        $financialPayment->delete();
    }    
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