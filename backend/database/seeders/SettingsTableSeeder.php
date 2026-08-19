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
            'about_text'          => 'Dressnio is a Dhaka-based fashion house curating premium stitched and unstitched wear for women — from elegant kurtas and suit sets to luxurious lehengas and hand-woven sarees.',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command->info('Settings ensured');
    }
}
