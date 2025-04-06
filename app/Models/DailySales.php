<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}