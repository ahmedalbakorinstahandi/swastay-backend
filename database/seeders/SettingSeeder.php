<?php

namespace Database\Seeders\General;

use App\Models\General\Setting;
use App\Models\Setting as ModelsSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ModelsSetting::updateOrCreate(
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
