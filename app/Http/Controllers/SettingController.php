<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
     
    public function index()
    {
        $settings = Setting::all();

        return ResponseService::response(
            [
                'success' => true,
                'data' => SettingResource::collection($settings),
                'status' => 200,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
