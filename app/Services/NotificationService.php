<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a Telegram message.
     *
     * @param string $message
     * @return bool
     */
    public static function sendTelegramMessage(string $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (empty($botToken) || empty($chatId)) {
            Log::warning('Telegram Bot Token or Chat ID is not configured.');
            return false;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Telegram API Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram Exception: ' . $e->getMessage());
            return false;
        }
    }
}
