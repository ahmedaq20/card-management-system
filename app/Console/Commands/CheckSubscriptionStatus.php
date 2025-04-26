<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\MikrotikUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Check for expiring subscriptions and send Telegram alerts';

    public function handle()
    {
        $notificationsSent = 0;
        $this->info('Starting subscription status check...');

        $activeUsers = MikrotikUser::where('end_date_of_subscription', '>=', now()->subDay())
                                  ->get();
        $inactiveUsers = MikrotikUser::where('end_date_of_subscription', '<', now()->subDay())
                                  ->get();
     if($inactiveUsers){
        foreach ($inactiveUsers as $user) {
            $message ="⛔ انتهاء اشتراك: لقد انتهى اشتراك المستخدم {$user->username} (تاريخ الانتهاء:
            الخدمة متوقفة حالياً، يرجى التجديد لاستعادة الخدمة.";
            $this->sendTelegramNotification($message);
            Log::info("Sent subscription notification for user {$user->id}: {$message}");
            $notificationsSent++;
        }
        
     }

        if ($activeUsers->isEmpty()) {
            $this->info('No active subscriptions found.');
            return;
        }

      

        foreach ($activeUsers as $user) {
            $notificationSent = $this->checkUserSubscription($user);

            if ($notificationSent) {
                $notificationsSent++;
            }
        }

        $this->info("Completed. Sent {$notificationsSent} notifications.");
    }

    protected function checkUserSubscription(MikrotikUser $user): bool
    {
       
            $endDate = Carbon::parse($user->end_date_of_subscription)->startOfDay();
           
            $daysRemaining = now()->startOfDay()->diffInDays($endDate, false);
            $message = $this->getNotificationMessage($daysRemaining, $endDate,$user);
        
       

        if ($message) {
            $this->sendTelegramNotification($message);
            Log::info("Sent subscription notification for user {$user->id}: {$message}");
            return true;
        }

        return false;
    }

    protected function getNotificationMessage(int $daysRemaining, Carbon $endDate,$user): ?string
    {
        $formattedDate = $endDate->format('Y-m-d');

        return match (true) {
            $daysRemaining === 2 => "🔔 تنبيه: اشتراك المستخدم {$user->username} سينتهي بعد يومين (تاريخ الانتهاء:
            {$formattedDate})\n\nيرجى التجديد للحفاظ على الخدمة.",
        
            $daysRemaining === 1 => "🔔 تنبيه عاجل: اشتراك المستخدم {$user->username} سينتهي غداً (تاريخ الانتهاء:
            {$formattedDate})\n\nنرجو التجديد فوراً لتجنب انقطاع الخدمة.",
        
            $daysRemaining === 0 => "⚡ تنبيه نهائي: اشتراك المستخدم {$user->username} ينتهي اليوم! (تاريخ الانتهاء:
            {$formattedDate})\n\nيجب التجديد الآن لتجنب إيقاف الخدمة.",
        
            $daysRemaining < 0  => "⛔ انتهاء اشتراك: لقد انتهى اشتراك المستخدم {$user->username} (تاريخ الانتهاء:
            {   $formattedDate})\n\nالخدمة متوقفة حالياً، يرجى التجديد لاستعادة الخدمة.",
    default => null,
    };
    }

    protected function sendTelegramNotification(string $message): void
    {
    $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
    $chatId = config('services.telegram.chat_id', env('TELEGRAM_CHAT_ID'));

    if (empty($botToken)) {
    Log::error('Telegram bot token not configured');
    return;
    }

    if (empty($chatId)) {
    Log::error('Telegram chat ID not configured');
    return;
    }

    try {
    $response = Http::timeout(10)
    ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML',
    ]);

    if (!$response->successful()) {
    Log::error('Telegram API error: ' . $response->body());
                          }
    } catch (\Exception $e) {
    Log::error('Failed to send Telegram notification: ' . $e->getMessage());
                            }
    }
}