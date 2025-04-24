<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\NotifyInactiveSubscriptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
                     
        // Schedule::job(new NotifyInactiveSubscriptions, 'handle')->everyMinute();
       })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();


    //  public static function sendTelegramNotification(MikrotikUser $user): void
    // {
    //     $telegramToken = env('TELEGRAM_BOT_TOKEN'); // Add your bot token in the .env file
    //     $chatId = env('TELEGRAM_CHAT_ID'); // Add your chat ID in the .env file

    //     $message = "⚠️ تنبيه: حالة الاشتراك للمستخدم {$user->username} غير مفعلة.\n"
    //         . "📅 تاريخ الاشتراك: {$user->date_of_subscription}\n"
    //         . "💬 تعليق: {$user->comment}";

    //     Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
    //         'chat_id' => $chatId,
    //         'text' => $message,
    //     ]);
    // }