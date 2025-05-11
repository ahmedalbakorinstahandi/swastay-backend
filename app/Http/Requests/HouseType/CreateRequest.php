<?php

namespace App\Http\Requests\HouseType;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => LanguageService::translatableFieldRules('required|string|max:255'),
            'description' => LanguageService::translatableFieldRules('nullable|string'),
            'icon' => 'required|string|max:255',
            'is_visible' => 'required|boolean',
        ];
    }
}

