<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Http\Services\CityService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cityService ;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index()
    {
        $cities = $this->cityService->index();
        return response()->json(
            [
                'success' => true,
                'data' => CityResource::collection($cities)
            ]
        );
    }
}
