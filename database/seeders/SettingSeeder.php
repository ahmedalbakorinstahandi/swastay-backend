<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $settings = [
            [
                'key' => 'commission',
                'value' => 10,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'service_fee',
                'value' => 5,
                'type' => 'float',
                'allow_null' => true,
                'is_settings' => true,
            ],
        ];



        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'allow_null' => $setting['allow_null'],
                    'is_settings' => $setting['is_settings'],
                ]
            );
        }
    }
}
