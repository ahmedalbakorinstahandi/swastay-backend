<?php

namespace App\Http\Services;

use App\Models\City;

class CityService 
{
    public function index()
    {
        $query = City::all();

        return $query;
    }
}
