<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Which payment methods the store accepts, from admin settings.
 */
class PaymentMethods
{
    /**
     * @return array<string, array<string, mixed>> keyed by method code (cod, bkash, nagad, card)
     */
    public static function enabled(): array
    {
        $methods = [];

        if (Setting::read('payment_cod_enabled', '1') === '1') {
            $methods['cod'] = ['label' => 'Cash on delivery', 'online' => false];
        }

        foreach (['bkash' => 'bKash', 'nagad' => 'Nagad'] as $code => $label) {
            $number = (string) Setting::read("{$code}_number", '');

            if (Setting::read("payment_{$code}_enabled", '0') === '1' && $number !== '') {
                $methods[$code] = ['label' => $label, 'online' => true, 'manual' => true, 'number' => $number];
            }
        }

        if (app(SslcommerzService::class)->isConfigured()) {
            $methods['card'] = ['label' => 'Card or mobile banking', 'online' => true, 'manual' => false];
        }

        return $methods;
    }
}
