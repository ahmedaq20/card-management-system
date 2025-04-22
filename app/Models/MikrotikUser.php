<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MikrotikUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'phone',
        'user_in_network',
        'password_in_network',
        'last_ip_address',
        'last_mac',
        'date_of_subscription',
        'is_active',
        'comment',
    ];

    public function MikrotikPayment()
    {
        return $this->hasMany(MikrotikPayment::class, 'mikrotik_user_id');
    }

    /**
     * Calculate the total subscription payment.
     */
    // public function totalSubscriptionPayment(): float
    // {

    //     return dd($this->MikrotikPayment()->where('mikrotik_user_id',$this->id)->sum('amount'));
    // }

    public function getSubscriptionStatusAttribute(): string
    {
        if (!$this->date_of_subscription) {
            return 'inactive';
        }

        $expiryDate = Carbon::parse($this->date_of_subscription)->addMonth();

        return $expiryDate->isFuture() ? 1 : 0 ;
    }
}
