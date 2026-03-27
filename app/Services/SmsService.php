<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        $gatewayUrl = Setting::get('sms_gateway_url');
        $gatewayToken = Setting::get('sms_gateway_token');

        if ($gatewayUrl && $gatewayToken) {
            try {
                $response = Http::withToken($gatewayToken)
                    ->post($gatewayUrl, [
                        'to' => $phone,
                        'message' => $message,
                    ]);

                if ($response->successful()) {
                    return true;
                }

                Log::warning('SMS gateway response not successful', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (\Throwable $e) {
                Log::error('SMS gateway error: '.$e->getMessage());
            }
        }

        Log::info('[SMS Fallback] Sending to '.$phone.' message: '.$message);

        return false;
    }
}