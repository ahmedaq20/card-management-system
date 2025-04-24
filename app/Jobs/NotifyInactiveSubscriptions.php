<?php

namespace App\Jobs;

use App\Models\MikrotikUser;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyInactiveSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Fetch all users with inactive subscriptions
        $users = MikrotikUser::all();

        foreach ($users as $user) {
            if ($user->subscription_status === 0) {
                $users->sendTelegramNotification($user);
            }
        }
    }

    /**
     * Send a notification to Telegram.
     */
    protected function sendTelegramNotification(MikrotikUser $user): void
    {
        $telegramToken = env('TELEGRAM_BOT_TOKEN'); // Add your bot token in the .env file
        $chatId = env('TELEGRAM_CHAT_ID'); // Add your chat ID in the .env file

        $message = "⚠️ تنبيه: حالة الاشتراك للمستخدم {$user->username} غير مفعلة.\n"
            . "📅 تاريخ الاشتراك: {$user->date_of_subscription}\n"
            . "💬 تعليق: {$user->comment}";

        Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}