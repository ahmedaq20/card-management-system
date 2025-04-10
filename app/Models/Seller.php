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

    public function updateFinancialData()
{
    $totalCardsSold = $this->dailySales()->sum('quantity_sold'); // حسب اسم العمود عندك
    $totalAmountPaid = $this->payments()->sum('amount');
    $wholesalePrice = $this->wholesale_price ?? 0;

    $expectedAmount = $totalCardsSold * $wholesalePrice;
    $remainingDues = $expectedAmount - $totalAmountPaid;

    $this->update([
        'cards_sold' => $totalCardsSold,
        'amount_paid' => $totalAmountPaid,
        'remaining_dues' => $remainingDues,
        'payments' => $this->payments()->sum('amount'), // أو شيء آخر حسب ما تريده
    ]);
}

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