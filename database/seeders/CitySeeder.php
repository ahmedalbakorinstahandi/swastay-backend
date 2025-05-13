<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // syria cities with place_id
        $cities = [
            [
                'name' => ['en' => 'Idlib', 'ar' => 'إدلب'],
                'place_id' => 'ChIJe9JuzOYAJRURTMIfZS9-muU',
            ],
            [
                'name' => ['en' => 'Damascus', 'ar' => 'دمشق'],
                'place_id' => 'ChIJp8Y8QdzmGBURe4Gw1Yq45Wk',
            ],
            [
                'name' => ['en' => 'Aleppo', 'ar' => 'حلب'],
                'place_id' => 'ChIJyRcLwFr4LxURG7Z03NijjvY',
            ],
            [
                'name' => ['en' => 'Homs', 'ar' => 'حمص'],
                'place_id' => 'ChIJpygFseoOIxURmYZ6AohbZWU',
            ],
            [
                'name' => ['en' => 'Latakia', 'ar' => 'اللاذقية'],
                'place_id' => 'ChIJH1a_woscJBURK8NFXKztsts',
            ],
            [
                'name' => ['en' => 'Tartus', 'ar' => 'طرطوس'],
                'place_id' => 'ChIJrSFVGeZ9IRURfbu3JKtsRSI',
            ],
            [
                'name' => ['en' => 'Daraa', 'ar' => 'درعا'],
                'place_id' => 'ChIJ541uAaFfGRURnNQ1EnZBa_8',
            ],
            [
                'name' => ['en' => 'Deir ez-Zor', 'ar' => 'دير الزور'],
                'place_id' => 'ChIJrdvTFDMVSBURf8IhQoBoWp0',
            ],
            [
                'name' => ['en' => 'Raqqa', 'ar' => 'الرقة'],
                'place_id' => 'ChIJ5wzG5M4ZNxURtpqJDuBXRp0',
            ],
            [
                'name' => ['en' => 'Quneitra', 'ar' => 'القنيطرة'],
                'place_id' => 'ChIJnwY-za-0HhURI1amCDjWu7w',
            ],

            [
                'name' => ['en' => 'Al-Hasakah', 'ar' => 'الحسكة'],
                'place_id' => 'ChIJ-y3uLZF2CUARsFQkSn62NRc',
            ],
            [
                'name' => ['en' => 'Hama', 'ar' => 'حماة'],
                'place_id' => 'ChIJGS8F1ZOCJBURlu05HFjhbW4',
            ],
            [
                'name' => ['en' => 'As-Suwayda', 'ar' => 'السويداء'],
                'place_id' => 'ChIJEWNy8UWOGRURHhb4Yzns2iE',
            ],
            [
                'name' => ['en' => 'Rif Dimashq', 'ar' => 'ريف دمشق'],
                'place_id' => 'ChIJs9VVgNEXGBUR0BtGJWxQV-0',
            ],
        ];

        foreach ($cities as $city) {
            City::create([
                'name' => $city['name'],
                'place_id' => $city['place_id'],
            ]);
        }
    }
}
