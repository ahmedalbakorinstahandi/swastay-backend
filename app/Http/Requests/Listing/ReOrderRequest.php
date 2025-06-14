<?php

namespace App\Http\Requests\Listing;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class ReOrderRequest extends BaseFormRequest
{

    public function rules(): array
    {
        $rules = [
            'orders' => 'required|integer|exists:images,orders,deleted_at,NULL',
        ];

        return $rules;
    }
}
