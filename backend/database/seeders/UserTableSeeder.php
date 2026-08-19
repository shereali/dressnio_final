<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Seed the admin user and a demo customer (idempotent).
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@dressnio.com');
        $adminPassword = env('ADMIN_PASSWORD', 'dressnio@2026');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name'      => 'Dressnio Admin',
                'mobile_no' => '01974784848',
                'password'  => Hash::make($adminPassword),
            ]
        );

        Customer::firstOrCreate(
            ['mobile_no' => '01974784849'],
            [
                'name'     => 'Demo Customer',
                'email'    => 'customer@dressnio.com',
                'password' => Hash::make('customer@2026'),
            ]
        );

        $this->command->info('Admin + demo customer created');
    }
}
