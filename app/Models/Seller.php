<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'name', 'sales_point', 'phone', 'cards_sold',
        'amount_paid', 'remaining_dues', 'payments',
        'wholesale_price',
    ];


    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // public function dailySalesReports()
    // {
    //     return $this->hasMany(DailySales::class, 'seller_id');
    // }

    public function dailySales()
    {
        return $this->hasMany(DailySales::class); // علاقة مع جدول dailySales
    }


    public function payments()
    {
        return $this->hasMany(FinancialPayment::class);
    }

}