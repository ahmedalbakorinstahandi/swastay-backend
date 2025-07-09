<?php

namespace App\Http\Requests\Listing;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class AvailableDateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'not_available_dates' => 'nullable|array',
            'not_available_dates.*' => 'date_format:Y-m-d',
            'removed_not_available_dates' => 'nullable|array',
            'removed_not_available_dates.*' => 'date_format:Y-m-d',
        ];
    }
}
