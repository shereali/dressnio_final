<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Seed site configuration (idempotent by key).
     */
    public function run(): void
    {
        $settings = [
            'announcement'        => 'Free shipping on orders over ৳2,000 — nationwide delivery in 2-4 days!',
            'contact_phone'       => '+880 1974-784848',
            'contact_email'       => 'hello@dressnio.com',
            'contact_address'     => 'House 12, Road 5, Dhanmondi, Dhaka 1205',
            'facebook_url'        => 'https://www.facebook.com/Dressnio',
            'instagram_url'       => 'https://www.instagram.com/dressnio',
            'shipping_fee'        => '120',
            'free_shipping_above' => '2000',
            'bkash_number'        => '01974784848',
            'nagad_number'        => '01974784848',
            'payment_cod_enabled'   => '1',
            'payment_bkash_enabled' => '0',
            'payment_nagad_enabled' => '0',
            'sslcommerz_enabled'    => '0',
            'sslcommerz_sandbox'    => '1',
            'sslcommerz_store_id'   => '',
            'sms_enabled'        => '0',
            'sms_endpoint'       => 'https://bulksmsdhaka.net/api/otpsend',
            'sms_caller_id'       => '',
            'sms_otp_template'   => 'Your Dressnio code is {otp}. It is valid for {minutes} minutes. Do not share it with anyone.',
            'about_text'          => 'Dressnio is a Dhaka-based fashion house curating premium stitched and unstitched wear for women — from elegant kurtas and suit sets to luxurious lehengas and hand-woven sarees.',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command?->info('Settings ensured');
    }
}
