<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FinancialPayment;

class DailySales extends Model
{

    protected $table = 'dailySales';

    protected $fillable = [
        'date', 'seller_id', 'quantity_sold', 'amount_paid', 'notes',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    protected static function booted()
    {
        static::updated(function ($dailySale) {
            // Check if the amount_paid field was updated
            if ($dailySale->isDirty('amount_paid') || $dailySale->isDirty('date')) {
                // Update or create a corresponding record in FinancialPayments
                FinancialPayment::updateOrCreate(
                    [
                        'seller_id' => $dailySale->seller_id,
                        'date' => $dailySale->date,
                    ],
                    [
                        'amount' => $dailySale->amount_paid,
                        'date' => $dailySale->date,
                        'description' =>  $dailySale->notes,
                    ]
                   
                );
            }
        });
    }
}