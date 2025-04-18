<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class FinancialPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'seller_id',
        'amount',
        'description',
        'with_cards',
        'date',
        'daily_sales_id'
    ];

       // Accessor for with_cards attribute
       public function getWithCardsAttribute($value)
       {
           return $value == 1 ? 'مع بطاقات' : 'بدون بطاقات';
       }
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
    
    public function dailySales()
    {
        return $this->belongsTo(DailySales::class, 'daily_sales_id');
    }

    // public function FinancialPayments()
    // {
    //     return $this->belongsTo(FinancialPayments::class, 'daily_sales_id');
    // }

    protected static function booted()
    {
        static::updated(function ($FinancialPayment) {
            // Check if the amount_paid field was updated
            if ($FinancialPayment->isDirty('amount') || $FinancialPayment->isDirty('date') || $FinancialPayment->isDirty('description')) {
                // Update or create a corresponding record in FinancialPayments
            
                $dailySales = DailySales::where('id', $FinancialPayment->daily_sales_id) 
                ->first(); // جلب أول سجل فقط
                // If a record exists, update it

                if($dailySales){
                    $dailySales->update([
                        'amount_paid' => $FinancialPayment->amount,
                        'date' => $FinancialPayment->date,
                        'notes' =>  $FinancialPayment->description,
                    ]);
                }
            }
        });

        static::deleted(function ($financialPayment) {
            $dailySales = DailySales::where('id', $financialPayment->daily_sales_id)->first();
            if ($dailySales) {
                $dailySales->delete();
            }
        });
    }

}