<?php

namespace App\Http\Requests\ListingRule;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'allows_pets' => 'nullable|boolean',
            'allows_smoking' => 'nullable|boolean',
            'allows_parties' => 'nullable|boolean',
            'allows_children' => 'nullable|boolean',
            'remove_shoes' => 'nullable|boolean',
            'no_extra_guests' => 'nullable|boolean',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',

            'quiet_hours' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'restricted_rooms_note' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'garbage_disposal_note' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'pool_usage_note' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'forbidden_activities_note' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
        ];
    }
}
