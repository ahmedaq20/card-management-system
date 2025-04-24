<?php

namespace App\Console\Commands;

use App\Models\MikrotikUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Check for expired subscriptions and send Telegram alerts';

    public function handle()
    {
        $expiredUsers = MikrotikUser::whereDate('end_date_of_subscription', '<', now())->get();

        foreach ($expiredUsers as $user) {
            $this->sendTelegramMessage("⚠️ الاشتراك الخاص بـ {$user->username} منتهي منذ تاريخ {$user->end_date_of_subscription}");
        }

        $this->info('Checked subscriptions.');
    }

    // protected function sendTelegramMessage($message)
    // {
    //     $botToken = config('services.telegram.bot_token');
    //     $chatId = config('services.telegram.chat_id');

    //     Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
    //         'chat_id' => $chatId,
    //         'text' => $message,
    //     ]);
    // }

    protected function sendTelegramMessage($message): void
    {
        $telegramToken = env('TELEGRAM_BOT_TOKEN'); // Add your bot token in the .env file
        $chatId = env('TELEGRAM_CHAT_ID'); // Add your chat ID in the .env file


        Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }


}