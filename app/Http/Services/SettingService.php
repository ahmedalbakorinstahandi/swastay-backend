<?php

namespace App\Http\Services;

use App\Http\Permissions\SettingPermission;
use App\Models\Setting;
use App\Services\FilterService;
use App\Services\MessageService;

class SettingService
{
    public function index($data)
    {
        $query = Setting::query();

        $data['limit'] = 1000;

        $query = SettingPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['key', 'value'],
            [],
            ['created_at'],
            ['type', 'is_settings'],
            ['id', 'key'] // in_key[] = ['key1', 'key2']
        );
    }


    // multi data updates
    public function update($data)
    {
        foreach ($data as $item) {
            $setting = Setting::where('key', $item['key'])->first();
            if (!$setting) {
                MessageService::abort(404, 'messages.setting.item_not_found');
            }
            $setting->update(['value' => $item['value']]);
        }

        return [];
    }

    public function show($id)
    {
        $item = Setting::find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.setting.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        $data['is_settings'] = true;

        $setting = Setting::where('key', $data['key'])->first();
        if ($setting) {
            MessageService::abort(400, 'messages.setting.item_already_exists');
        }
        $setting = Setting::create($data);
        return $setting;
    }

    public function update($item, $data)
    {
        $item->update($data);
        
        return $item;
    }

    public function delete($item)
    {
        return $item->delete();
    }
}
