<?php

namespace App\Services;

use App\Models\GameSetting;
use Illuminate\Support\Facades\Http;

class TelegramNotifier
{
    public static function send(string $message): bool
    {
        try {
            $enabled = (string) GameSetting::get('telegram_enabled', '0') === '1';
            $token = (string) GameSetting::get('telegram_bot_token', (string) config('services.telegram.bot_token', ''));
            $chatId = (string) GameSetting::get('telegram_chat_id', (string) config('services.telegram.chat_id', ''));

            if (! $enabled || $token === '' || $chatId === '') {
                return false;
            }

            $url = "https://api.telegram.org/bot{$token}/sendMessage";

            $resp = Http::timeout(8)->asForm()->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            return $resp->successful() && (bool) data_get($resp->json(), 'ok', false);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
