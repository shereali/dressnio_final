<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends OTP messages through the SMS gateway configured in admin settings
 * (default: bulksmsdhaka.net OTP API).
 */
class SmsService
{
    public const DEFAULT_ENDPOINT = 'https://bulksmsdhaka.net/api/otpsend';

    public const DEFAULT_TEMPLATE = 'Your Dressnio code is {otp}. It is valid for {minutes} minutes. Do not share it with anyone.';

    public function isConfigured(): bool
    {
        return Setting::read('sms_enabled', '0') === '1'
            && (string) Setting::read('sms_api_key', '') !== ''
            && (string) Setting::read('sms_caller_id', '') !== '';
    }

    public function otpMessage(string $otp, int $minutes): string
    {
        $template = (string) Setting::read('sms_otp_template', self::DEFAULT_TEMPLATE) ?: self::DEFAULT_TEMPLATE;

        return str_replace(['{otp}', '{minutes}'], [$otp, (string) $minutes], $template);
    }

    /**
     * @return array{ok: bool, error: ?string}
     */
    public function send(string $number, string $message): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'error' => 'SMS is not configured.'];
        }

        $endpoint = (string) Setting::read('sms_endpoint', self::DEFAULT_ENDPOINT) ?: self::DEFAULT_ENDPOINT;

        try {
            $response = Http::timeout(10)->acceptJson()->get($endpoint, [
                'apikey'   => Setting::read('sms_api_key'),
                'callerID' => Setting::read('sms_caller_id'),
                'number'   => $number,
                'message'  => $message,
            ]);
        } catch (\Throwable $e) {
            // The request URL carries the API key, so only the exception class is logged.
            Log::warning('SMS gateway request failed', ['error' => class_basename($e)]);

            return ['ok' => false, 'error' => 'The SMS gateway could not be reached.'];
        }

        if (! $response->successful() || $this->bodyIndicatesFailure($response->body(), $response->json())) {
            Log::warning('SMS gateway rejected the message', [
                'status' => $response->status(),
                'body'   => mb_substr($response->body(), 0, 200),
            ]);

            return ['ok' => false, 'error' => 'The SMS gateway rejected the message.'];
        }

        return ['ok' => true, 'error' => null];
    }

    private function bodyIndicatesFailure(string $body, mixed $json): bool
    {
        if (is_array($json)) {
            foreach (['status', 'success'] as $key) {
                if (array_key_exists($key, $json)) {
                    $value = is_string($json[$key]) ? strtolower($json[$key]) : $json[$key];

                    return in_array($value, [false, 0, '0', 'false', 'error', 'failed', 'fail'], true);
                }
            }
        }

        return (bool) preg_match('/invalid|insufficient|unauthori[sz]ed|failed|error/i', $body);
    }
}
