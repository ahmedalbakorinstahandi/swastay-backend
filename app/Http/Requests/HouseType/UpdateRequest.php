<?php

namespace App\Http\Requests\HouseType;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => LanguageService::translatableFieldRules('nullable|string|max:255'),
            'description' => LanguageService::translatableFieldRules('nullable|string'),
            'icon' => 'nullable|string|max:255',
            'is_visible' => 'nullable|boolean',
        ];
    }
}
