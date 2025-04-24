<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
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
        'start_date_of_subscription',
        'end_date_of_subscription',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(callback: function ($model) {
            $model->start_date_of_subscription = $model->date_of_subscription;
            $model->end_date_of_subscription = \Carbon\Carbon::parse($model->date_of_subscription)->addDays(30);
        });

        // static::updating(function ($model) {
        //     $model->start_date_of_subscription = $model->date_of_subscription;
        //     $model->end_date_of_subscription = \Carbon\Carbon::parse($model->date_of_subscription)->addDays(30);
        // });
    }

    public function getSubscriptionStatusAttribute(): string
   {
    if (!$this->date_of_subscription) {
        return 'inactive';
    }

    $expiryDate = Carbon::parse($this->start_date_of_subscription)->addMonth();

    //  to calculte remaining days
        // $daysRemaining = now()->diffInDays($expiryDate, false);

    $status = $expiryDate->isFuture() ? 1 : 0;

    // if($status===0){

    // $this->sendTelegramNotification();
    // }

    return $status;
    }

    /**
 * Send a notification to Telegram.
 */
    protected function sendTelegramNotification(): void
    {
        $telegramToken = env('TELEGRAM_BOT_TOKEN'); // Add your bot token in the .env file
        $chatId = env('TELEGRAM_CHAT_ID'); // Add your chat ID in the .env file

        $message = "⚠️ تنبيه: حالة الاشتراك للمستخدم {$this->username} غير مفعلة.\n"
            . "📅 تاريخ الاشتراك: {$this->date_of_subscription}\n"
            . "💬 تعليق: {$this->comment}";

        Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}