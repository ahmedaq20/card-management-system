<?php

namespace App\Models;

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
}