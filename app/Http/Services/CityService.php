<?php

namespace App\Http\Services;

use App\Models\City;

class CityService
{
    public function index()
    {
        $query = City::orderBy('orders', 'asc')->where('availability', true)->get();

        return $query;
    }
}
