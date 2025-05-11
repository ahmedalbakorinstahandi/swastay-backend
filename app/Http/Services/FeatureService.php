<?php

namespace App\Http\Services;

use App\Http\Permissions\FeaturePermission;
use App\Models\Feature;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;

class FeatureService
{
    public function index($filters = [])
    {
        $query = Feature::query();

        $query = FeaturePermission::filterIndex($query);

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
        $feature = Feature::find($id);
        if (!$feature) {
            MessageService::abort(404, 'messages.feature.not_found');
        }
        return $feature;
    }

    public function create($data)
    {
        $data = LanguageService::prepareTranslatableData($data, new Feature);
        return Feature::create($data);
    }

    public function update(Feature $feature, array $data)
    {
        $data = LanguageService::prepareTranslatableData($data, $feature);
        $feature->update($data);
        return $feature;
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();
    }
}
