<?php

namespace App\Http\Requests\Listing;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class AvailableDateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'available_dates' => 'required|array',
            'available_dates.*' => 'date_format:Y-m-d',
            'removed_available_dates' => 'array',
            'removed_available_dates.*' => 'date_format:Y-m-d',
        ];
    }
}
