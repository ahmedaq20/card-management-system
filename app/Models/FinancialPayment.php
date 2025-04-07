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
        'with_cards'
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
    
}