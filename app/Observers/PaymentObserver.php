<?php

namespace App\Observers;

use App\Models\FinancialPayment;

class PaymentObserver
{
    /**
     * Handle the FinancialPayment "created" event.
     */
    public function created(FinancialPayment $financialPayment): void
    {
        $financialPayment->seller->updateFinancialData();

    }

    /**
     * Handle the FinancialPayment "updated" event.
     */
    public function updated(FinancialPayment $financialPayment): void
    {
        $financialPayment->seller->updateFinancialData();
        
    }

    /**
     * Handle the FinancialPayment "deleted" event.
     */
    public function deleted(FinancialPayment $financialPayment): void
    {
        $financialPayment->seller->updateFinancialData();
        
    }

    /**
     * Handle the FinancialPayment "restored" event.
     */
    public function restored(FinancialPayment $financialPayment): void
    {
        //
    }

    /**
     * Handle the FinancialPayment "force deleted" event.
     */
    public function forceDeleted(FinancialPayment $financialPayment): void
    {
        //
    }
}