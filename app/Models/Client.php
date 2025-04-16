<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   protected $guarded=[]; 
    public function scopeActive($query)
{
    return $query->where('status', 'active'); // or ->where('is_active', true);
}
}