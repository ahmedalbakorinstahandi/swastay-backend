<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // i need 3 users : admin, host and guest

        $users = [
            [
                'id' => 1,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'wallet_balance' => 0.0,
                'avatar' => null,
                'email' => 'admin@gmail.com',
                'email_verified' => true,
                'country_code' => '+1',
                'phone_number' => '2025550191',
                'phone_verified' => true,
                'password' => Hash::make('password'),
                'role' => 'admin',
                'id_verified' => 'none',
                'bank_details' => null,
                'status' => 'active',
                'otp' => null,
                'otp_expire_at' => null,
                'is_verified' => true,
            ],
            [
                'id' => 2,
                'first_name' => 'Host',
                'last_name' => 'User',
                'wallet_balance' => 100.0,
                'avatar' => null,
                'email' => 'host@gmail.com',
                'email_verified' => true,
                'country_code' => '+1',
                'phone_number' => '2025550192',
                'phone_verified' => true,
                'password' => Hash::make('password'),
                'role' => 'user',
                'id_verified' => 'approved',
                'bank_details' => 'Bank XYZ, Account 123456789',
                'status' => 'active',
                'otp' => null,
                'otp_expire_at' => null,
                'is_verified' => true,
            ],
            [
                'id' => 3,
                'first_name' => 'Guest',
                'last_name' => 'User',
                'wallet_balance' => 50.0,
                'avatar' => null,
                'email' => 'guest@gmail.com',
                'email_verified' => false,
                'country_code' => '+1',
                'phone_number' => '2025550193',
                'phone_verified' => false,
                'password' => Hash::make('password'),
                'role' => 'user',
                'id_verified' => 'none',
                'bank_details' => null,
                'status' => 'active',
                'otp' => Str::random(6),
                'otp_expire_at' => now()->addMinutes(10),
                'is_verified' => false,
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
