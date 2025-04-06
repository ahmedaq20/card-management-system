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
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}