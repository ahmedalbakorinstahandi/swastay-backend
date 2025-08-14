<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@gmail.com')->first();
        $user->country_code = '+963';
        $user->phone = '965737371';
        $user->password = Hash::make('Sawa.Stay_SYR2025!');
        $user->save();

        $user->tokens()->delete();
    }
}
