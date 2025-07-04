<?php

namespace App\Http\Requests\Listing;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        $rules = [
            'title' => LanguageService::translatableFieldRules('required|string|max:255'),
            'description' => LanguageService::translatableFieldRules('required|string|max:1500'),
            'house_type_id' => 'required|exists:house_types,id,deleted_at,NULL',
            'property_type' => 'required|in:House,Apartment,Guesthouse',
            'price' => 'required|numeric|min:0',
            'price_weekend' => 'nullable|numeric|min:0',
            'guests_count' => 'required|integer|min:1',
            'bedrooms_count' => 'required|integer|min:1',
            'beds_count' => 'required|integer|min:1',
            'bathrooms_count' => 'required|numeric|min:0.5',
            'booking_capacity' => 'required|integer|min:1',
            'is_contains_cameras' => 'required|boolean',
            'camera_locations' =>
            request('is_contains_cameras') ? LanguageService::translatableFieldRules('required|string|max:350') : LanguageService::translatableFieldRules('nullable|string|max:350'),
            // 'noise_monitoring_device' => 'required|boolean',
            // 'weapons_on_property' => 'required|boolean',
            'floor_number' => 'required|integer|min:0',
            'min_booking_days' => 'required|integer|min:1',
            'max_booking_days' => 'required|integer|min:1|max:730',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',

            'features' => 'nullable|array',
            'features.*' => 'required|exists:features,id,deleted_at,NULL',
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|exists:categories,id,deleted_at,NULL',
            'location.latitude' => 'required|numeric',
            'location.longitude' => 'required|numeric',
            'location.street_address' => 'required|string|max:255',
            'location.extra_address' => 'required|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string|max:100',

            'rule' => 'nullable|array',
            'rule.allows_families_only' => 'nullable|boolean',
            

        ];


        $user = User::auth();

        if ($user->isAdmin() || $user->isEmployee()) {
            $rules['host_id'] = 'required|exists:users,id,deleted_at,NULL';
            $rules['vip'] = 'nullable|boolean';
            $rules['starts'] = 'nullable|integer|min:0|max:5';
        }


        return $rules;
    }
}
