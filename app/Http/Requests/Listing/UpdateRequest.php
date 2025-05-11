<?php

namespace App\Http\Requests\Listing;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        $user = User::auth();

        $rules = [
            'title' => LanguageService::translatableFieldRules('nullable|string|max:255'),
            'description' => LanguageService::translatableFieldRules('nullable|string'),
            'house_type_id' => 'nullable|exists:house_types,id,deleted_at,NULL',
            'property_type' => 'nullable|in:House,Apartment,Guesthouse',
            'price' => 'nullable|numeric|min:0',
            'guests_count' => 'nullable|integer|min:1',
            'bedrooms_count' => 'nullable|integer|min:1',
            'beds_count' => 'nullable|integer|min:1',
            'bathrooms_count' => 'nullable|numeric|min:0.5',
            'booking_capacity' => 'nullable|integer|min:1',
            'is_contains_cameras' => 'nullable|boolean',
            'camera_locations' => LanguageService::translatableFieldRules('nullable|string|max:350'),
            'noise_monitoring_device' => 'nullable|boolean',
            'weapons_on_property' => 'nullable|boolean',
            'floor_number' => 'nullable|integer|min:1',
            'min_booking_days' => 'nullable|integer|min:1',
            'max_booking_days' => 'nullable|integer|min:1|max:730',
            'features' => 'nullable|array',
            'features.*' => 'required|exists:features,id,deleted_at,NULL',
            'categories' => 'nullable|array|min:1',
            'categories.*' => 'required|exists:categories,id,deleted_at,NULL',
            'location' => 'nullable|array',
            'location.latitude' => 'required|numeric',
            'location.longitude' => 'required|numeric',
            'location.extra_address' => 'required|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'required|string|max:100',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'required|exists:images,id,deleted_at,NULL',
        ];

        if ($user->isAdmin()) {
            $rules_admin = [
                'commission' => 'nullable|numeric|min:0|max:100',
                'status' => 'nullable|in:in_review,approved,paused,rejected',
                'is_published' => 'nullable|boolean',
            ];
        }

        $rules = array_merge($rules, $rules_admin ?? []);

        return $rules;
    }
}
