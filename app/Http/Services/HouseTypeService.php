<?php

namespace App\Http\Services;

use App\Http\Permissions\HouseTypePermission;
use App\Models\HouseType;
use App\Models\User;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;

class HouseTypeService
{
    public function index($filters = [])
    {
        $query = HouseType::query();

        $query = HouseTypePermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $filters,
            ['name', 'description'],
            [],
            [],
            ['is_visible'],
            []
        );
    }

    public function show($id)
    {
        $houseType = HouseType::find($id);

        if (!$houseType) {
            MessageService::abort(404, 'messages.house_type.not_found');
        }

        return $houseType;
    }

    public function create($data)
    {
        $data = LanguageService::prepareTranslatableData($data, new HouseType);
        return HouseType::create($data);
    }

    public function update(HouseType $houseType, array $data)
    {
        $data = LanguageService::prepareTranslatableData($data, $houseType);
        $houseType->update($data);

        return $houseType;
    }

    public function destroy(HouseType $houseType)
    {
        $houseType->delete();
    }
}
