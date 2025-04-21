<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MikrotikPayment extends Model
{
    protected $fillable = [
        'mikrotik_user_id',
        'amount',
        'payment_date',
        'description',
    ];

    public function mikrotikUser()
    {
        return $this->belongsTo(MikrotikUser::class, 'mikrotik_user_id');
    }
}